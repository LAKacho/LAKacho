<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Путь к autoload.php, если используете Composer

// Подключение к базе данных 1 (где хранится список студентов и их email)
$conn1 = new mysqli('localhost', 'username1', 'password1', 'database1');
if ($conn1->connect_error) {
    die("Connection to database1 failed: " . $conn1->connect_error);
}

// Подключение к базе данных 2 (где хранятся оценки)
$conn2 = new mysqli('localhost', 'username2', 'password2', 'database2');
if ($conn2->connect_error) {
    die("Connection to database2 failed: " . $conn2->connect_error);
}

// Шаг 1: Выбираем студентов с оценкой «2» за последнюю неделю
$sql1 = "SELECT student_id FROM xtvs WHERE grade = 2 AND date >= CURDATE() - INTERVAL 7 DAY";
$result1 = $conn2->query($sql1);

if ($result1->num_rows > 0) {
    // Очищаем таблицу xtvsdva перед новой вставкой
    $conn1->query("TRUNCATE TABLE xtvsdva");

    // Создание экземпляра PHPMailer для отправки писем
    $mail = new PHPMailer(true);

    // Настройки для отправки писем
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com';  // Укажите SMTP сервер
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@example.com';  // Ваш email
        $mail->Password = 'your_email_password';    // Пароль к email
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('your_email@example.com', 'Администрация');
        $mail->Subject = 'Уведомление о низкой оценке';

        // Шаг 2: Для каждого студента с оценкой 2 находим email и отправляем письмо
        while ($row = $result1->fetch_assoc()) {
            $student_id = $row['student_id'];

            // Запрос для получения email из таблицы list
            $sql2 = "SELECT email FROM list WHERE student_id = ?";
            $stmt = $conn1->prepare($sql2);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $stmt->bind_result($email);
            $stmt->fetch();
            $stmt->close();

            // Если email найден, записываем в xtvsdva и отправляем уведомление
            if ($email) {
                // Вставка данных в таблицу xtvsdva
                $insert_sql = "INSERT INTO xtvsdva (student_id, email, grade) VALUES (?, ?, ?)";
                $insert_stmt = $conn1->prepare($insert_sql);
                $grade = 2;
                $insert_stmt->bind_param("isi", $student_id, $email, $grade);
                $insert_stmt->execute();
                $insert_stmt->close();

                // Отправка уведомления по email
                $mail->addAddress($email);
                $mail->Body = "Уважаемый студент,\n\nВаша последняя оценка составляет «2». Пожалуйста, обратите внимание на свои результаты.\n\nС уважением,\nАдминистрация";
                $mail->send();
                $mail->clearAddresses();
            }
        }
    } catch (Exception $e) {
        echo "Ошибка при отправке письма: {$mail->ErrorInfo}";
    }

    echo "Данные успешно обновлены и уведомления отправлены.";
} else {
    echo "Нет студентов с оценкой «2» за последнюю неделю.";
}

$sql = "SELECT login, email, test_name FROM dva";
$result = $conn1->query($sql);

if ($result->num_rows > 0) {
    // Инициализируем PHPMailer и настраиваем SMTP
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'ssl://smtp.yandex.ru'; // SMTP сервер
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_email@example.com'; // Ваш SMTP логин
        $mail->Password   = 'your_password';          // Ваш SMTP пароль
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 465;
        $mail->setFrom('your_email@example.com', 'УЦ АТБ');
        $mail->Subject    = 'Уведомление о низкой оценке';
    } catch (Exception $e) {
        die("Ошибка настройки PHPMailer: " . $mail->ErrorInfo);
    }

    // Проходим по каждому результату и отправляем email
    while ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $test_name = $row['test_name'];

        try {
            // Добавляем адрес получателя
            $mail->addAddress($email);
            
            // Формируем тело письма
            $mail->Body = "Уважаемый сотрудник,\n\nУ вас низкая оценка за тест: $test_name. Пожалуйста, свяжитесь с вашим руководителем.";

            // Отправляем письмо
            $mail->send();
            $mail->clearAddresses(); // Очищаем список адресов для следующего письма

            echo "Письмо успешно отправлено на $email с тестом $test_name<br>";
        } catch (Exception $e) {
            echo "Ошибка при отправке письма на $email: " . $mail->ErrorInfo . "<br>";
        }
    }
} else {
    echo "Нет данных для отправки уведомлений.";
}



$conn1->close();
$conn2->close();
?>
