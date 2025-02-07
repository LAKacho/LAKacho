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
$conn2 = new mysqli("localhost", "u159215", "", "intern_shB");
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

    // $mail = new PHPMailer(true);
    // try {
    //     $mail->isSMTP();
    //     $mail->Host       = 'ssl://smtp.yandex.ru'; 
    //     $mail->SMTPAuth   = true;
    //     $mail->Username   = ''; 
    //     $mail->Password   = '';           
    //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    //     $mail->Port       = 465;
    //     $mail->setFrom('your_email@example.com', 'УЦ АТБ');
    //     $mail->Subject    = 'Уведомление о низкой оценке';
    // } catch (Exception $e) {
    //     die("Ошибка настройки PHPMailer: " . $mail->ErrorInfo);
    // }

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
            $insert_sql = "INSERT INTO dva (login, email, grade) VALUES (?, ?, ?)";
            $insert_stmt = $conn1->prepare($insert_sql);
            if (!$insert_stmt) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $grade = 2;
            $insert_stmt->bind_param("isi", $login, $email, $grade);
            $insert_stmt->execute();
            $insert_stmt->close();

            // try {
            //     $mail->addAddress($email);
            //     $mail->Body = "Уважаемый сотрудник,\n\nУ вас низкая оценка за последние уроки. Пожалуйста, свяжитесь с вашим руководителем.";
            //     $mail->send();
            //     $mail->clearAddresses();
            //     echo "Письмо успешно отправлено на $email<br>";
            // } catch (Exception $e) {
            //     echo "Ошибка при отправке письма на $email: " . $mail->ErrorInfo . "<br>";
            // }
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }

    echo "Данные успешно обновлены, но уведомления не отправляются.";
} else {
    echo "Нет студентов с оценкой 2 за неделю.";
}

$conn1->close();
$conn2->close();
?>
