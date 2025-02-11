<?php
// Подключаем автозагрузчик Composer для использования PHPMailer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Подключение к базе данных 1 (куда будут записываться данные и email)
$conn1 = new mysqli("localhost", "u159215", "", "b159215_intern");
if ($conn1->connect_error) {
    die("Ошибка соединения с базой 1: " . $conn1->connect_error);
}

// Подключение к базе данных 2 (откуда берутся оценки)
$conn2 = new mysqli("localhost", "u159215", "", "1test_system");
if ($conn2->connect_error) {
    die("Ошибка соединения с базой 2: " . $conn2->connect_error);
}

// Подключение к базе данных 3
$conn3 = new mysqli("localhost", "u159215", "", "123");
if ($conn3->connect_error) {
    die("Ошибка соединения с базой 3: " . $conn3->connect_error);
}

// Функция для записи оценок "2" в таблицу `dva`
function process_grades($result, $conn1, $test_name) {
    // Очищаем таблицу 'dva' в базе 1 перед новой записью
    if (!$conn1->query("TRUNCATE TABLE dva")) {
        die("Ошибка очистки таблицы 'dva': " . $conn1->error);
    }

    // Обрабатываем каждую запись
    while ($row = $result->fetch_assoc()) {
        $login = $row['user_login'];

        // Запрос для получения email по user_login (соответствующему полю tb) из таблицы list базы 1
        $sql2 = "SELECT email FROM list WHERE tb = ?";
        $stmt = $conn1->prepare($sql2);
        if (!$stmt) {
            echo "Ошибка подготовки запроса для login = $login: " . $conn1->error;
            continue;
        }
        $stmt->bind_param("i", $login);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();

        if ($email) {
            // Записываем информацию в таблицу 'dva'
            $insert_sql = "INSERT INTO dva (login, email, grade, test_name) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn1->prepare($insert_sql);
            if (!$insert_stmt) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $grade = 2;
            $insert_stmt->bind_param("isis", $login, $email, $grade, $test_name);
            $insert_stmt->execute();
            $insert_stmt->close();
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}

// Запрос на выборку пользователей с оценкой 2 за последнюю неделю из базы данных 2
$sql1 = "SELECT user_login, lesson_name
         FROM xtvr_sesions24
         WHERE lesson_name IN (303, 305)
           AND lesson_result = 2
           AND time_in >= CURDATE() - INTERVAL 7 DAY";
$result1 = $conn2->query($sql1);
if (!$result1) {
    die("Ошибка выполнения запроса к базе 2: " . $conn2->error);
}
if ($result1->num_rows > 0) {
    while ($row = $result1->fetch_assoc()) {
        $test_name = "Тест: " . $row['lesson_name']; // Название теста
        process_grades($result1, $conn1, $test_name);
    }
} else {
    echo "Нет студентов с оценкой 2 за последнюю неделю в базе 2.<br>";
}

// Запросы на выборку оценок 2 из таблиц базы данных 3
$queries = [
    'rkm' => 'grade_rkm',
    'sess1' => 'grade_sess1',
    'sess_dpk' => 'grade_sess_dpk',
    'sess_sr' => 'grade_sess_sr',
    'seas_tdvs_tren' => 'grade_seas_tdvs_tren'
];

foreach ($queries as $table => $grade_column) {
    $sql = "SELECT user_login, test_name_column
            FROM $table
            WHERE $grade_column = 2
              AND date >= CURDATE() - INTERVAL 7 DAY";
    $result = $conn3->query($sql);
    if (!$result) {
        echo "Ошибка выполнения запроса к таблице $table: " . $conn3->error . "<br>";
        continue;
    }
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $test_name = "Тест: " . $row['test_name_column']; // Название теста
            process_grades($result, $conn1, $test_name);
        }
    } else {
        echo "Нет студентов с оценкой 2 за последнюю неделю в таблице $table базы 3.<br>";
    }
}

// Закрываем подключения
$conn1->close();
$conn2->close();
$conn3->close();
?>