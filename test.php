<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Получаем ID пользователя
$userId = $_SESSION['user_id'];

// Получение доступных тестов для пользователя из таблицы `user_test_access`
$stmt = $pdo->prepare("SELECT test_id FROM user_test_access WHERE user_id = :user_id AND access_level = 1");
$stmt->execute(['user_id' => $userId]);
$userTestAccess = $stmt->fetchAll(PDO::FETCH_COLUMN); // Список test_id, доступных пользователю

// Директория с тестами
$testDir = __DIR__ . '/data/tests/';

// Проверка, выбран ли тест
$testId = $_GET['test_id'] ?? null;

if (!$testId) {
    echo "<h2>Выберите доступный тест:</h2>";
    foreach ($userTestAccess as $allowedTestId) {
        $testFilePath = $testDir . $allowedTestId . '.json';
        if (file_exists($testFilePath)) {
            echo "<a href='test.php?test_id=" . urlencode($allowedTestId) . "'>" . htmlspecialchars($allowedTestId) . "</a><br>";
        }
    }
    exit;
}

// Проверка, что выбранный тест доступен пользователю
if (!in_array($testId, $userTestAccess)) {
    die("У вас нет доступа к этому тесту или тест не найден.");
}

// Путь к файлу теста
$testFilePath = $testDir . $testId . '.json';

if (!file_exists($testFilePath)) {
    die("Тест не найден.");
}

// Загружаем тест из JSON файла
$testData = json_decode(file_get_contents($testFilePath), true);

if (!$testData || !isset($testData['time_limit'], $testData['display_questions'], $testData['questions'])) {
    die("Ошибка загрузки теста. Проверьте структуру JSON файла.");
}

// Устанавливаем время теста и количество вопросов
$timeLimit = $testData['time_limit'];
$totalQuestions = $testData['display_questions'];

// Инициализация теста и вопросов
if (!isset($_SESSION['questions']) || !is_array($_SESSION['questions'])) {
    $questions = $testData['questions'];
    shuffle($questions);
    $_SESSION['questions'] = array_slice($questions, 0, $totalQuestions);
    $_SESSION['current_question'] = 0;
    $_SESSION['start_time'] = time();
    $_SESSION['test_id'] = $testId; // сохраняем ID теста для результатов
    $_SESSION['test_title'] = $testId; // сохраняем название теста
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестирование: <?= htmlspecialchars($testId) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        // Таймер для теста
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
        // Проверяем, существует ли текущий вопрос
        $currentQuestionIndex = $_SESSION['current_question'] ?? 0;
        $question = $_SESSION['questions'][$currentQuestionIndex] ?? null;

        if ($question): // Если вопрос существует, отображаем его
        ?>

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
    // Проверка, что массив `questions` и `current_question` установлены корректно
    if (isset($_SESSION['questions'], $_SESSION['current_question'])) {
        $currentQuestionIndex = $_SESSION['current_question'];
        $question = $_SESSION['questions'][$currentQuestionIndex] ?? null;

        // Проверка и сохранение ответа пользователя
        if ($question) {
            $userAnswer = $_POST['answer'] ?? [];
            $correctAnswer = $question['correct'] ?? [];

            // Проверка правильности ответа
            $isCorrect = ($question['type'] === 'multiple')
                ? empty(array_diff($userAnswer, $correctAnswer)) && empty(array_diff($correctAnswer, $userAnswer))
                : in_array($userAnswer[0], $correctAnswer);

            $_SESSION['answers'][$currentQuestionIndex] = [
                'answer' => $userAnswer,
                'correct' => $isCorrect
            ];

            // Переход к следующему вопросу
            $_SESSION['current_question']++;

            // Если это последний вопрос, переход к результатам
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
