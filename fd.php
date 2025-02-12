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
            $insert_sql1 = "INSERT INTO dva (login, email, grade) VALUES (?, ?, ?)";
            $insert_stmt1 = $conn1->prepare($insert_sql1);
            if (!$insert_stmt1) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $grade = 2;
            $insert_stmt1->bind_param("isi", $login, $email, $grade);
            $insert_stmt1->execute();
            $insert_stmt1->close();

            $insert_sql2 = "INSERT INTO test_name (login) VALUES (?)";
            $insert_stmt2 = $conn1->prepare($insert_sql2);
            if (!$insert_stmt2) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $insert_stmt2->bind_param("s", $login);
            $insert_stmt2->execute();
            $insert_stmt2->close();

            echo "Login $login успешно записан в таблицу test_name.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }

    echo "Данные успешно обновлены.";
} else {
    echo "Нет студентов с оценкой 2 за неделю.";
}

$conn1->close();
$conn2->close();
?>