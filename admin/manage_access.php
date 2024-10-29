<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

// Получение списка пользователей
$userQuery = $pdo->query("SELECT id, username FROM users");
$users = $userQuery->fetchAll(PDO::FETCH_ASSOC);

// Получение списка JSON файлов в директории tests
$testDir = __DIR__ . '/../data/tests/';
$testFiles = array_filter(scandir($testDir), function($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'json';
});

// Обновление доступа к тестам
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['user_id'];
    $selectedTests = $_POST['test_access'] ?? [];

    // Удаление текущих записей доступа
    $pdo->prepare("DELETE FROM user_test_access WHERE user_id = :user_id")->execute(['user_id' => $userId]);

    // Добавление новых записей для выбранных тестов
    $stmt = $pdo->prepare("INSERT INTO user_test_access (user_id, test_id, access_level) VALUES (:user_id, :test_id, 1)");
    foreach ($selectedTests as $testFile) {
        $testId = pathinfo($testFile, PATHINFO_FILENAME); // Имя файла без расширения используется как test_id
        $stmt->execute(['user_id' => $userId, 'test_id' => $testId]);
    }

    echo "<p style='color: green;'>Доступ обновлен успешно.</p>";
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
            $testId = pathinfo($testFile, PATHINFO_FILENAME); // Имя файла без расширения как test_id

            // Проверка, есть ли доступ к тесту у выбранного пользователя
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

        <button type="submit">Обновить доступ</button>
    </form>
</body>
</html>
