<?php 

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

// Запрос на получение студентов с оценкой 2 за последнюю неделю
$sql1 = "SELECT user_login
        FROM xtvr_sesions24 
        WHERE lesson_name IN (303, 305) AND lesson_result <= 72 AND time_in >= CURDATE() - INTERVAL 7 DAY";

$result1 = $conn2->query($sql1);

if ($result1->num_rows > 0) {
    // Очищаем таблицу перед новой записью
    $conn1->query("TRUNCATE TABLE dva");

    // Создаем объект PHPMailer, но временно не отправляем письма
    //$mail = new PHPMailer(true);

    //try {
        // Настройки для отправки почты
        // $mail->isSMTP();
        // $mail->Host = 'ssl://smtp.yandex.ru';
        // $mail->SMTPAuth = true;
        // $mail->Username = ''; // Укажите ваш SMTP логин
        // $mail->Password = ''; // Укажите ваш SMTP пароль
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        // $mail->Port = 465; 
        // $mail->setFrom('your_email@example.com', 'УЦ АТБ');
        // $mail->Subject = 'Уведомление о низкой оценке';

        // Обрабатываем результаты запроса
        while ($row = $result1->fetch_assoc()) {
            $login = $row['user_login'];

            // Запрос для получения email из другой базы данных
            $sql2 = "SELECT email FROM intern_all WHERE tb_nomer = ?";
            $stmt = $conn1->prepare($sql2);
            $stmt->bind_param("i", $login);
            $stmt->execute();
            $stmt->bind_result($email);
            $stmt->fetch();
            $stmt->close();

            if ($email) {
                // Записываем информацию в таблицу 'dva'
                $insert_sql = "INSERT INTO dva (login, email, grade) VALUES (?, ?, ?)";
                $insert_stmt = $conn1->prepare($insert_sql);
                $grade = 2;
                $insert_stmt->bind_param("isi", $login, $email, $grade);
                $insert_stmt->execute();
                $insert_stmt->close();

                // Временно не отправляем уведомление
                // $mail->addAddress($email);
                // $mail->Body = "Уважаемый сотрудник, у вас низкая оценка за последние уроки. Пожалуйста, свяжитесь с вашим руководителем.";
                // $mail->send();
                // $mail->clearAddresses();
            }
        }
    //} catch (Exception $e) {
        //echo "Ошибка при отправке письма: {$mail->ErrorInfo}";
    //}

    echo "Данные успешно обновлены. Отправка писем отключена.";
} else {
    echo "Нет студентов с оценкой 2 за неделю.";
}

$conn1->close();
$conn2->close();

?>