<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Получаем ID пользователя и теста
$userId = $_SESSION['user_id'];
$testId = $_GET['test_id'] ?? null;

// Проверка, выбран ли тест
if (!$testId) {
    echo "<h2>Выберите доступный тест:</h2>";
    $stmt = $pdo->prepare("SELECT test_id FROM user_test_access WHERE user_id = :user_id AND access_level = 1 AND completed = FALSE");
    $stmt->execute(['user_id' => $userId]);
    $userTestAccess = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($userTestAccess as $allowedTestId) {
        echo "<a href='test.php?test_id=" . urlencode($allowedTestId) . "'>" . htmlspecialchars($allowedTestId) . "</a><br>";
    }
    exit;
}

// Проверка, что тест доступен и не завершен
$stmt = $pdo->prepare("SELECT access_level, completed FROM user_test_access WHERE user_id = :user_id AND test_id = :test_id");
$stmt->execute(['user_id' => $userId, 'test_id' => $testId]);
$access = $stmt->fetch();

if (!$access || $access['access_level'] != 1 || $access['completed']) {
    die("У вас нет доступа к этому тесту или тест уже завершен.");
}

// Директория с тестами
$testDir = __DIR__ . '/data/tests/';
$testFilePath = $testDir . $testId . '.json';

if (!file_exists($testFilePath)) {
    die("Тест не найден.");
}

// Загрузка теста из JSON файла
$testData = json_decode(file_get_contents($testFilePath), true);
$timeLimit = $testData['time_limit'];
$totalQuestions = $testData['display_questions'];

// Инициализация теста
if (!isset($_SESSION['questions']) || $_SESSION['test_id'] != $testId) {
    $questions = $testData['questions'];
    shuffle($questions);
    $_SESSION['questions'] = array_slice($questions, 0, $totalQuestions);
    $_SESSION['current_question'] = 0;
    $_SESSION['start_time'] = time();
    $_SESSION['test_id'] = $testId;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Тестирование: <?= htmlspecialchars($testId) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        let timeLeft = <?= $timeLimit ?>;
        setInterval(function() {
            if (timeLeft <= 0) {
                alert("Время теста истекло!");
                window.location.href = "results.php";
            } else {
                timeLeft--;
                document.getElementById("timer").innerText = "Оставшееся время: " + timeLeft + " сек";
            }
        }, 1000);
    </script>
</head>
<body>
    <div class="test-container">
        <h2><?= htmlspecialchars($testId) ?></h2>
        <p id="timer">Оставшееся время: <?= $timeLimit ?> сек</p>

        <?php
        $currentQuestionIndex = $_SESSION['current_question'] ?? 0;
        $question = $_SESSION['questions'][$currentQuestionIndex] ?? null;

        if ($question): ?>
            <form action="test.php?test_id=<?= urlencode($testId) ?>" method="post">
                <p><strong>Вопрос <?= $currentQuestionIndex + 1 ?>:</strong> <?= htmlspecialchars($question['question']) ?></p>
                
                <?php foreach ($question['options'] as $index => $option) : ?>
                    <label>
                        <input type="<?= $question['type'] === 'multiple' ? 'checkbox' : 'radio' ?>" 
                               name="answer[]" 
                               value="<?= $index ?>">
                        <?= htmlspecialchars($option) ?>
                    </label><br>
                <?php endforeach; ?>

                <button type="submit">Ответить</button>
            </form>
        <?php else: ?>
            <p>Вопрос не найден. Пожалуйста, перезагрузите страницу или начните тест заново.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['questions'], $_SESSION['current_question'])) {
        $currentQuestionIndex = $_SESSION['current_question'];
        $question = $_SESSION['questions'][$currentQuestionIndex] ?? null;

        if ($question) {
            $userAnswer = $_POST['answer'] ?? [];
            $correctAnswer = $question['correct'] ?? [];

            $isCorrect = ($question['type'] === 'multiple')
                ? empty(array_diff($userAnswer, $correctAnswer)) && empty(array_diff($correctAnswer, $userAnswer))
                : in_array($userAnswer[0], $correctAnswer);

            $_SESSION['answers'][$currentQuestionIndex] = [
                'answer' => $userAnswer,
                'correct' => $isCorrect
            ];

            $_SESSION['current_question']++;

            if ($_SESSION['current_question'] >= count($_SESSION['questions'])) {
                header('Location: results.php');
                exit;
            } else {
                header("Location: test.php?test_id=" . urlencode($testId));
                exit;
            }
        } else {
            echo "Ошибка: текущий вопрос не найден.";
        }
    } else {
        echo "Ошибка: вопросы не инициализированы.";
    }
}
?>
