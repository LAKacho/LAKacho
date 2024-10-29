<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

$file = $_GET['file'] ?? '';
$testPath = "../data/tests/$file";

if (!file_exists($testPath)) {
    die("Файл теста не найден.");
}

// Загрузка данных теста из JSON
$testData = json_decode(file_get_contents($testPath), true);

// Добавление вопроса в тест
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $questionText = $_POST['question'];
    $questionType = $_POST['type'];
    $options = array_filter($_POST['options'], 'strlen'); // Удаление пустых вариантов
    $correctAnswers = array_map('intval', $_POST['correct']);

    $testData['questions'][] = [
        'id' => count($testData['questions']) + 1,
        'question' => $questionText,
        'type' => $questionType,
        'options' => $options,
        'correct' => $correctAnswers
    ];

    // Сохранение обновленного теста в JSON
    file_put_contents($testPath, json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: edit_test.php?file=" . urlencode($file));
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование теста</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="edit-test-container">
        <h2>Редактирование теста: <?= htmlspecialchars($testData['title']) ?></h2>
        
        <h3>Вопросы</h3>
        <ul>
            <?php foreach ($testData['questions'] as $question): ?>
                <li>
                    <strong><?= htmlspecialchars($question['question']) ?></strong>
                    <ul>
                        <?php foreach ($question['options'] as $index => $option): ?>
                            <li><?= htmlspecialchars($option) ?> <?= in_array($index, $question['correct']) ? '(Верный)' : '' ?></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>

        <h3>Добавить вопрос</h3>
        <form action="edit_test.php?file=<?= urlencode($file) ?>" method="post">
            <label>Текст вопроса:</label>
            <input type="text" name="question" required>
            
            <label>Тип вопроса:</label>
            <select name="type">
                <option value="single">Один верный ответ</option>
                <option value="multiple">Несколько верных ответов</option>
            </select>

            <label>Варианты ответа:</label>
            <input type="text" name="options[]" placeholder="Вариант 1" required>
            <input type="text" name="options[]" placeholder="Вариант 2" required>
            <input type="text" name="options[]" placeholder="Вариант 3">
            <input type="text" name="options[]" placeholder="Вариант 4">

            <label>Правильные ответы (введите номера):</label>
            <input type="text" name="correct[]" placeholder="0">
            <input type="text" name="correct[]" placeholder="1">

            <button type="submit">Добавить вопрос</button>
        </form>
    </div>
</body>
</html>
