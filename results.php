<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['test_id']) || !isset($_SESSION['start_time'])) {
    header('Location: index.php');
    exit;
}

// Получаем данные о тесте
$userId = $_SESSION['user_id'];
$testId = $_SESSION['test_id'];
$startTime = $_SESSION['start_time'];
$endTime = time();
$totalTime = $endTime - $startTime;

// Подсчитываем результаты
$totalQuestions = count($_SESSION['questions']);
$correctAnswers = 0;

foreach ($_SESSION['answers'] as $answerData) {
    if ($answerData['correct']) {
        $correctAnswers++;
    }
}

$scorePercentage = ($correctAnswers / $totalQuestions) * 100;
$passingScore = $_SESSION['passing_score'] ?? 50;  // Порог прохождения из JSON
$isPassed = $scorePercentage >= $passingScore;

// Сохраняем результат в базу данных
$stmt = $pdo->prepare("INSERT INTO test_results (user_id, test_id, start_time, end_time, total_time, correct_answers, total_questions, score, passed) VALUES (:user_id, :test_id, :start_time, :end_time, :total_time, :correct_answers, :total_questions, :score, :passed)");
$stmt->execute([
    'user_id' => $userId,
    'test_id' => $testId,
    'start_time' => date('Y-m-d H:i:s', $startTime),
    'end_time' => date('Y-m-d H:i:s', $endTime),
    'total_time' => $totalTime,
    'correct_answers' => $correctAnswers,
    'total_questions' => $totalQuestions,
    'score' => $scorePercentage,
    'passed' => $isPassed ? 1 : 0
]);

// Очищаем сессию теста после завершения
unset($_SESSION['questions'], $_SESSION['current_question'], $_SESSION['answers'], $_SESSION['start_time'], $_SESSION['test_id'], $_SESSION['test_title']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты теста</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="results-container">
        <h2>Результаты теста: <?= htmlspecialchars($testId) ?></h2>
        <p><strong>Правильных ответов:</strong> <?= $correctAnswers ?> из <?= $totalQuestions ?></p>
        <p><strong>Процент прохождения:</strong> <?= round($scorePercentage, 2) ?>%</p>
        <p><strong>Порог прохождения:</strong> <?= $passingScore ?>%</p>
        <p><strong>Общее время:</strong> <?= gmdate("H:i:s", $totalTime) ?></p>
        
        <?php if ($isPassed): ?>
            <p style="color: green;"><strong>Поздравляем, вы прошли тест!</strong></p>
        <?php else: ?>
            <p style="color: red;"><strong>К сожалению, вы не прошли тест.</strong></p>
        <?php endif; ?>

        <a href="index.php">Вернуться на главную</a>
    </div>
</body>
</html>
