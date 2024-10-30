<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

// Папка для хранения JSON файлов с тестами
$testDirectory = '../data/tests/';
$tests = array_diff(scandir($testDirectory), ['..', '.']);

// Подключение к базе данных
require '../db.php';

// Получение количества попыток для каждого теста
$testAttempts = [];
foreach ($tests as $testFile) {
    $testId = pathinfo($testFile, PATHINFO_FILENAME); // Предполагается, что имя файла соответствует test_id
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_attempts WHERE test_id = ?");
    $stmt->execute([$testId]);
    $testAttempts[$testFile] = $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление тестами</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="manage-tests-container">
        <h2>Управление тестами</h2>
        
        <!-- Список доступных тестов с количеством попыток -->
        <h3>Существующие тесты</h3>
        <ul>
            <?php foreach ($tests as $testFile): ?>
                <li>
                    <a href="edit_test.php?file=<?= urlencode($testFile) ?>">
                        <?= htmlspecialchars($testFile) ?> (Попыток: <?= $testAttempts[$testFile] ?? 0 ?>)
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Форма для создания нового теста -->
        <h3>Создать новый тест</h3>
        <form action="create_test.php" method="post">
            <label>Название теста:</label>
            <input type="text" name="test_title" required>
            <label>Максимальное время (секунды):</label>
            <input type="number" name="time_limit" required>
            <label>Количество вопросов для отображения:</label>
            <input type="number" name="display_questions" required>
            <label>Проходной балл (%):</label>
            <input type="number" name="passing_score" required>
            <button type="submit">Создать тест</button>
        </form>
    </div>
</body>
</html>
