<?php
session_start();
include 'db.php';
include 'utils.php';
require 'vendor/autoload.php';

// Конфигурация сроков действия тестов
define("TEST_DURATIONS", [
    "Старшинство" => 180,
    "Наставничество" => 360,
    "Базовые навыки" => 360
]);

// Шаг 1: Пользователь выбрал тест
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test']) && !isset($_POST['tab_number'])) {
    $_SESSION['test'] = $_POST['test'];
    displayTabNumberForm();
    exit;
}

// Шаг 2: Пользователь ввел табельный номер
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tab_number'])) {
    $tab_number = "B" . $_POST['tab_number']; // Добавляем "B" перед номером
    $test_name = $_SESSION['test'] ?? null;

    if (!$test_name) {
        displayError("Ошибка: тест не выбран.");
        exit;
    }

    try {
        // Проверка табельного номера и получение имени пользователя
        $user_info = getUserInfoByTabNumber($pdo, $tab_number);
        if (!$user_info) {
            displayError("Ваш табельный номер отсутствует в системе. Обратитесь к администратору.");
            exit;
        }

        $username = $user_info['username'];
        $name = $user_info['password']; // Используем `password` как имя

        // Проверка ограничений и сроков действия
        $message = checkTestEligibility($pdo, $tab_number, $test_name);
        if ($message !== true) {
            displayError($message);
            exit;
        }

        // Запись в базу данных
        $current_date = date('Y-m-d H:i:s');
        registerTest($pdo, $tab_number, $test_name, $current_date);

        // Уведомление администраторам
        include 'email_notify.php';
        sendNotification($tab_number, $test_name, $current_date);

        // Успешное сообщение
        displaySuccessMessage($test_name);
    } catch (Exception $e) {
        logError($e->getMessage());
        displayError("Произошла ошибка: " . $e->getMessage());
    }
}

// Форма ввода табельного номера
function displayTabNumberForm() {
    echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Ввод табельного номера</title>
            <link rel='stylesheet' href='styles.css'>
        </head>
        <body>
            <div class='container'>
                <header>
                    <h1 class='title'>Введите ваш табельный номер</h1>
                </header>
                <div class='content'>
                    <form action='process.php' method='POST' class='form'>
                        <label for='tab_number' class='label'>Табельный номер:</label>
                        <input type='number' id='tab_number' name='tab_number' placeholder='Только цифры' required class='input'>
                        <button type='submit' class='button'>Записаться</button>
                    </form>
                </div>
            </div>
        </body>
        </html>
    ";
}

// Проверка условий записи на тест
function checkTestEligibility($pdo, $tab_number, $test_name) {
    $today = date('Y-m-d');
    $required_interval = TEST_DURATIONS[$test_name] ?? 1; // По умолчанию 1 день для других тестов

    // Получение последней записи
    $stmt = $pdo->prepare("SELECT MAX(date_time) AS last_date FROM test_registrations WHERE tab_number = ? AND test_name = ?");
    $stmt->execute([$tab_number, $test_name]);
    $last_record = $stmt->fetch();

    // Получение имени пользователя из базы данных
    $stmt_user = $pdo->prepare("SELECT password FROM users WHERE username = ?");
    $stmt_user->execute([$tab_number]);
    $user = $stmt_user->fetch();
    $name = $user ? $user['password'] : 'Неизвестный пользователь';

    if ($last_record && $last_record['last_date']) {
        $last_date = new DateTime($last_record['last_date']);
        $current_date = new DateTime();

        // Проверяем интервал с последней записью
        $interval = $current_date->diff($last_date)->days;

        if ($interval < $required_interval) {
            $next_available_date = $last_date->modify("+$required_interval days")->format('Y-m-d');
            return "Уважаемый $name, вы не можете записаться на тест \"$test_name\". Следующая доступная дата: $next_available_date.";
        }
    }

    return true; // Разрешаем запись
}

// Получение информации о пользователе по табельному номеру
function getUserInfoByTabNumber($pdo, $tab_number) {
    $stmt = $pdo->prepare("SELECT username, password FROM users WHERE username = ?");
    $stmt->execute([$tab_number]);
    return $stmt->fetch();
}

// Регистрация теста в базе данных
function registerTest($pdo, $tab_number, $test_name, $current_date) {
    $stmt = $pdo->prepare("INSERT INTO test_registrations (tab_number, test_name, date_time) VALUES (?, ?, ?)");
    $stmt->execute([$tab_number, $test_name, $current_date]);
}

// Общая функция для рендеринга страниц
function renderPage($title, $content) {
    echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>$title</title>
            <link rel='stylesheet' href='styles.css'>
        </head>
        <body>
            <div class='container'>
                <header>
                    <h1 class='title'>$title</h1>
                </header>
                <div class='content'>
                    $content
                </div>
            </div>
        </body>
        </html>
    ";
}

// Успешное сообщение
function displaySuccessMessage($test_name) {
    renderPage("Запись успешна", "
        <p class='success'>Вы успешно записались на тест <b>$test_name</b>.</p>
        <form action='index.php' method='GET'>
            <button type='submit' class='button'>Вернуться на главное меню</button>
        </form>
    ");
}

// Вывод информации с именем пользователя
function displayError($username_or_message) {
    renderPage("Информация", "
        <p class='error'>Ваше имя: <b>$username_or_message</b></p>
        <form action='index.php' method='GET'>
            <button type='submit' class='button'>Вернуться на главное меню</button>
        </form>
    ");
}
?>
