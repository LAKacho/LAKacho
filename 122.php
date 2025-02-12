<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Подключение к базе данных 1 (куда будут записываться данные и email)
$conn1 = new mysqli("localhost", "u159215", "", "1test_system");
if ($conn1->connect_error) {
    die("Ошибка соединения с базой 1: " . $conn1->connect_error);
}

// Подключение к базе данных 2 (откуда берутся оценки)
$conn2 = new mysqli("localhost", "u159215", "","intern_shB");
if ($conn2->connect_error) {
    die("Ошибка соединения с базой 2: " . $conn2->connect_error);
}

// Подключение к базе данных 3 (с таблицей rkm)
$conn3 = new mysqli("localhost", "u159215", "", "another_database");
if ($conn3->connect_error) {
    die("Ошибка соединения с базой 3: " . $conn3->connect_error);
}

$sql1 = "SELECT user_login
         FROM xtvr_sesions24
         WHERE lesson_name IN (303, 305)
           AND lesson_result <= 72
           AND time_in >= CURDATE() - INTERVAL 7 DAY";

$result1 = $conn2->query($sql1);
if (!$result1) {
    die("Ошибка выполнения запроса: " . $conn2->error);
}

if ($result1->num_rows > 0) {
    if (!$conn1->query("TRUNCATE TABLE dva")) {
        die("Ошибка очистки таблицы 'dva': " . $conn1->error);
    }

    while ($row = $result1->fetch_assoc()) {
        $login = $row['user_login'];

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
            $insert_sql = "INSERT INTO dva (login, email, grade, test_name) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn1->prepare($insert_sql);
            if (!$insert_stmt) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $grade = 2;
            $test_name = "xtvs";
            $insert_stmt->bind_param("ssis", $login, $email, $grade, $test_name);
            $insert_stmt->execute();
            $insert_stmt->close();

            echo "Login $login успешно записан в таблицу dva.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}

$sql3 = "SELECT user_login
         FROM rkm
         WHERE ocenka = 2
           AND time_in >= CURDATE() - INTERVAL 7 DAY";

$result3 = $conn3->query($sql3);
if (!$result3) {
    die("Ошибка выполнения запроса: " . $conn3->error);
}

if ($result3->num_rows > 0) {
    while ($row = $result3->fetch_assoc()) {
        $login = $row['user_login'];

        $sql4 = "SELECT email FROM list WHERE tb = ?";
        $stmt = $conn1->prepare($sql4);
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
            $insert_sql = "INSERT INTO dva (login, email, grade, test_name) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn1->prepare($insert_sql);
            if (!$insert_stmt) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $grade = 2;
            $test_name = "rkm";
            $insert_stmt->bind_param("ssis", $login, $email, $grade, $test_name);
            $insert_stmt->execute();
            $insert_stmt->close();

            echo "Login $login успешно записан в таблицу dva.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}

echo "Данные успешно обновлены.";

$conn1->close();
$conn2->close();
$conn3->close();
?>