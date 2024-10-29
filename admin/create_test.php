<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['test_title'];
    $timeLimit = (int)$_POST['time_limit'];
    $displayQuestions = (int)$_POST['display_questions'];
    $passingScore = (int)$_POST['passing_score'];

    // Новый JSON-шаблон теста
    $testData = [
        'title' => $title,
        'time_limit' => $timeLimit,
        'display_questions' => $displayQuestions,
        'passing_score' => $passingScore,
        'questions' => []
    ];

    // Сохранение нового теста как JSON
    $filename = '../data/tests/' . preg_replace('/[^a-zA-Z0-9-_]/', '_', strtolower($title)) . '.json';
    file_put_contents($filename, json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header('Location: manage_tests.php');
    exit;
}
?>
