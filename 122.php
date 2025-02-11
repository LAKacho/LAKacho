<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Подключение к базам данных
$conn1 = new mysqli("localhost", "u159215", "ZsX0seor!", "intern_shB");
if ($conn1->connect_error) {
    die("Ошибка соединения с базой 1: " . $conn1->connect_error);
}

$conn2 = new mysqli("localhost", "u159215", "ZsX0seor!", "1test_system");
if ($conn2->connect_error) {
    die("Ошибка соединения с базой 2: " . $conn2->connect_error);
}

$conn3 = new mysqli("localhost", "u159215", "ZsX0seor!", "123");
if ($conn3->connect_error) {
    die("Ошибка соединения с базой 3: " . $conn3->connect_error);
}

// Функция для обработки оценок и записи в таблицу `dva`
function process_grades($result, $conn1, $test_name) {
    while ($row = $result->fetch_assoc()) {
        $login = $row['user_login'];

        // Получаем email пользователя
        $sql7 = "SELECT email FROM list WHERE tb = ?";
        $stmt = $conn1->prepare($sql7);
        if (!$stmt) {
            echo "Ошибка подготовки запроса для login = $login: " . $conn1->error;
            continue;
        }
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();

        if ($email) {
            // Вставляем данные в таблицу `dva`
            $insert_sql = "INSERT INTO dva (login, email, grade, test_name) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn1->prepare($insert_sql);
            if (!$insert_stmt) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $grade = 2;
            $insert_stmt->bind_param("ssis", $login, $email, $grade, $test_name); 
            $insert_stmt->execute();
            $insert_stmt->close();

            echo "Данные для пользователя $login успешно записаны.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}

// Запросы к таблицам

// xtvs
$sql1 = "SELECT user_login FROM xtvr_sesions24 WHERE lesson_name IN (303, 305) AND lesson_result = 2 AND time_in >= CURDATE() - INTERVAL 7 DAY";
$result1 = $conn2->query($sql1);
if ($result1->num_rows > 0) {
    process_grades($result1, $conn1, "xtvs");
}

// rkm
$sql2 = "SELECT user_login FROM rkm WHERE ocenka = 2 AND time_in >= CURDATE() - INTERVAL 7 DAY";
$result2 = $conn3->query($sql2);
if ($result2->num_rows > 0) {
    process_grades($result2, $conn1, "rkm");
}

// papk
$sql4 = "SELECT user_login FROM sess_dpk WHERE level2 = 'test_dpk' AND error2 = 2 AND date >= CURDATE() - INTERVAL 7 DAY";
$result4 = $conn3->query($sql4);
if ($result4->num_rows > 0) {
    process_grades($result4, $conn1, "papk");
}

// pasr
$sql5 = "SELECT user_login FROM sess_sr WHERE level2 = 'test' AND error2 = 2 AND date >= CURDATE() - INTERVAL 7 DAY";
$result5 = $conn3->query($sql5);
if ($result5->num_rows > 0) {
    process_grades($result5, $conn1, "pasr");
}

// tdvs
$sql6 = "SELECT user_login FROM seas_tdvs_tren WHERE error1 = 2 AND date >= CURDATE() - INTERVAL 7 DAY";
$result6 = $conn3->query($sql6);
if ($result6->num_rows > 0) {
    process_grades($result6, $conn1, "tdvs");
}

// Закрываем соединения
$conn1->close();
$conn2->close();
$conn3->close();

echo "Данные успешно обновлены.";
?>