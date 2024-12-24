<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Настройки SMTP
function sendNotification($tab_number, $test_name, $current_date) {
    $mail = new PHPMailer(true);

    try {
        // Сервер SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Замените на адрес вашего SMTP-сервера
        $mail->SMTPAuth = true;
        $mail->Username = 'terj71221@gmail.com'; // Ваша почта
        $mail->Password = ''; // Ваш пароль
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Отправитель
        $mail->setFrom('terj71221@gmail.com', 'Test System'); // Замените на вашу почту и имя

        // Получатели
        $adminEmails = ['terj71221@icloud.com', 'admin2@example.com', 'admin3@example.com'];
        foreach ($adminEmails as $adminEmail) {
            $mail->addAddress($adminEmail);
        }

        // Тема письма с кодировкой UTF-8
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Новая запись на тестирование';
        
        // Тело письма с кодировкой UTF-8
        $mail->isHTML(true);
        $mail->Body = "
            <p>Добрый день,</p>
            <p>Пользователь с табельным номером <b>$tab_number</b> записался на тест <b>$test_name</b>.</p>
            <p>Дата и время: $current_date</p>
        ";

        // Отправка письма
        $mail->send();
        echo "Сообщение успешно отправлено.";
    } catch (Exception $e) {
        // Логирование ошибок для дальнейшей диагностики
        error_log("Ошибка отправки письма: {$mail->ErrorInfo}");
        echo "Ошибка при отправке сообщения.";
    }
}
?>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Настройки SMTP
function sendNotification($tab_number, $test_name, $current_date) {
    $mail = new PHPMailer(true);

    try {
        // Сервер SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Замените на адрес вашего SMTP-сервера
        $mail->SMTPAuth = true;
        $mail->Username = 'terj71221@gmail.com'; // Ваша почта
        $mail->Password = ''; // Ваш пароль
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Отправитель
        $mail->setFrom('terj71221@gmail.com', 'Test System'); // Замените на вашу почту и имя

        // Получатели
        $adminEmails = ['terj71221@icloud.com', 'admin2@example.com', 'admin3@example.com'];
        foreach ($adminEmails as $adminEmail) {
            $mail->addAddress($adminEmail);
        }

        // Установка кодировки для письма (UTF-8)
        $mail->CharSet = 'UTF-8';

        // Тема и текст письма
        $mail->isHTML(true);
        $mail->Subject = 'Новая запись на тестирование';
        $mail->Body = "
            <p>Добрый день,</p>
            <p>Пользователь с табельным номером <b>$tab_number</b> записался на тест <b>$test_name</b>.</p>
            <p>Дата и время: $current_date</p>
        ";

        // Отправка письма
        $mail->send();
    } catch (Exception $e) {
        error_log("Ошибка отправки письма: {$mail->ErrorInfo}");
    }
}
?>