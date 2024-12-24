<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendNotification($tab_number, $test_name, $current_date) {
    // Проверка входных параметров
    if (empty($tab_number) || empty($test_name) || empty($current_date)) {
        throw new InvalidArgumentException('Все параметры должны быть заполнены');
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP конфигурация
        $smtp_config = [
            'host' => 'smtp.gmail.com',
            'username' => 'terj71221@gmail.com',
            'password' => '',
            'from_email' => 'terj71221@gmail.com',
            'from_name' => 'Test System'
        ];

        // Настройка SMTP
        $mail->isSMTP();
        $mail->Host = $smtp_config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_config['username'];
        $mail->Password = $smtp_config['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Настройка отправителя
        $mail->setFrom($smtp_config['from_email'], $smtp_config['from_name']);

        // Настройка получателей
        $adminEmails = [
            'terj71221@icloud.com',
            'admin2@example.com',
            'admin3@example.com'
        ];

        foreach ($adminEmails as $adminEmail) {
            if (filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($adminEmail);
            }
        }

        // Настройки письма
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        
        // Очистка входных данных
        $tab_number = htmlspecialchars($tab_number, ENT_QUOTES, 'UTF-8');
        $test_name = htmlspecialchars($test_name, ENT_QUOTES, 'UTF-8');
        $current_date = htmlspecialchars($current_date, ENT_QUOTES, 'UTF-8');

        $mail->Subject = 'Новая запись на тестирование';
        $mail->Body = "
            <p>Добрый день,</p>
            <p>Пользователь с табельным номером <b>{$tab_number}</b> записался на тест <b>{$test_name}</b>.</p>
            <p>Дата и время: {$current_date}</p>
        ";

        // Добавление текстовой версии письма
        $mail->AltBody = "Добрый день,\n\n" .
            "Пользователь с табельным номером {$tab_number} записался на тест {$test_name}.\n" .
            "Дата и время: {$current_date}";

        if (!$mail->send()) {
            throw new Exception("Ошибка отправки: " . $mail->ErrorInfo);
        }

        return true;

    } catch (Exception $e) {
        error_log("Ошибка отправки письма: " . $e->getMessage());
        return false;
    }
}

// Пример использования:
// try {
//     $result = sendNotification('12345', 'PHP Test', date('Y-m-d H:i:s'));
//     if (!$result) {
//         // Обработка ошибки
//     }
// } catch (InvalidArgumentException $e) {
//     // Обработка неверных входных данных
// }
?>