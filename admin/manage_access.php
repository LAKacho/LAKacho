<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

// Функция для сохранения ответа пользователя
function saveUserAnswer($userId, $testId, $questionId, $answer) {
    global $pdo;

    $stmt = $pdo->prepare("INSERT INTO user_answers (user_id, test_id, question_id, answer, answer_time) 
                           VALUES (:user_id, :test_id, :question_id, :answer, NOW())");
    $stmt->execute([
        'user_id' => $userId,
        'test_id' => $testId,
        'question_id' => $questionId,
        'answer' => $answer
    ]);
}

// Пример вызова функции для записи ответа пользователя
// saveUserAnswer(1, 'test1', 101, 'A'); // user_id = 1, test_id = 'test1', question_id = 101, answer = 'A'

// Получение списка пользователей
$userQuery = $pdo->query("SELECT id, username FROM users");
$users = $userQuery->fetchAll(PDO::FETCH_ASSOC);

// Получение списка JSON файлов в директории tests
$testDir = __DIR__ . '/../data/tests/';
$testFiles = array_filter(scandir($testDir), function($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'json';
});

// Обновление доступа к тестам
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_access'])) {
    $userId = $_POST['user_id'];
    $selectedTests = $_POST['test_access'] ?? [];

    $pdo->prepare("DELETE FROM user_test_access WHERE user_id = :user_id")->execute(['user_id' => $userId]);

    $stmt = $pdo->prepare("INSERT INTO user_test_access (user_id, test_id, access_level) VALUES (:user_id, :test_id, 1)");
    foreach ($selectedTests as $testFile) {
        $testId = pathinfo($testFile, PATHINFO_FILENAME);
        $stmt->execute(['user_id' => $userId, 'test_id' => $testId]);
    }

    echo "<p style='color: green;'>Доступ обновлен успешно.</p>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['import_tests'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $filePath = $_FILES['csv_file']['tmp_name'];
        assignTestsFromExcel($filePath);
    } else {
        echo "<p style='color: red;'>Ошибка загрузки файла.</p>";
    }
}

function assignTestsFromExcel($filePath) {
    global $pdo;
    
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        fgetcsv($handle, 1000, ";");
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $username = trim($data[0]);
            $test_id = trim($data[1]);

            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user_id = $stmt->fetchColumn();

            if (!$user_id) {
                $stmt = $pdo->prepare("INSERT INTO users (username) VALUES (:username)");
                $stmt->execute(['username' => $username]);
                $user_id = $pdo->lastInsertId();
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_test_access WHERE user_id = :user_id AND test_id = :test_id");
            $stmt->execute(['user_id' => $user_id, 'test_id' => $test_id]);
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                $stmt = $pdo->prepare("INSERT INTO user_test_access (user_id, test_id, access_level) VALUES (:user_id, :test_id, 1)");
                $stmt->execute(['user_id' => $user_id, 'test_id' => $test_id]);
            }
        }
        fclose($handle);
        echo "<p style='color: green;'>Назначение тестов завершено!</p>";
    } else {
        echo "<p style='color: red;'>Ошибка открытия файла.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление доступом к тестам</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Назначение доступа к тестам</h2>

    <form method="post">
        <label>Выберите пользователя:</label>
        <select name="user_id" required>
            <option value="">-- Выберите пользователя --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Выберите доступные тесты:</label>
        <?php foreach ($testFiles as $testFile): ?>
            <?php
            $testId = pathinfo($testFile, PATHINFO_FILENAME);

            $isChecked = false;
            if (!empty($_POST['user_id'])) {
                $userId = $_POST['user_id'];
                $accessCheck = $pdo->prepare("SELECT COUNT(*) FROM user_test_access WHERE user_id = :user_id AND test_id = :test_id AND access_level = 1");
                $accessCheck->execute(['user_id' => $userId, 'test_id' => $testId]);
                $isChecked = $accessCheck->fetchColumn() > 0;
            }
            ?>
            <div>
                <input type="checkbox" name="test_access[]" value="<?= htmlspecialchars($testFile) ?>" <?= $isChecked ? 'checked' : '' ?>>
                <?= htmlspecialchars($testId) ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" name="update_access">Обновить доступ</button>
    </form>

    <h3>Импорт доступа пользователей к тестам из CSV (формат: username;test_id)</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="csv_file" accept=".csv" required>
        <button type="submit" name="import_tests">Импортировать</button>
    </form>
</body>
</html>
