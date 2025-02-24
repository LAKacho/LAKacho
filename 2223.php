<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn1 = new mysqli("localhost", "superadmin", "Solar1710$", "1test_system");
if ($conn1->connect_error) {
    die("Ошибка соединения с базой 1: " . $conn1->connect_error);
}

$limit = 190; 
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0; 

$sql11 = "SELECT login, email, test_name FROM dva LIMIT $limit OFFSET $offset";
$result9 = $conn1->query($sql11);

if ($result9->num_rows > 0) {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

  
    $smtpAccounts = [
        [
            'host' => 'ssl://smtp.yandex.ru',
            'username' => 'metyolckin.c@yandex.ru',
            'password' => 'tcxqiebivfofroup',
            'from' => 'metyolckin.c@yandex.ru',
        ],
        [
            'host' => 'ssl://smtp.yandex.ru',
            'username' => 'admin@educationalcenter.aeromash.ru',
            'password' => 'YN48qMc1',
            'from' => 'admin@educationalcenter.aeromash.ru',
        ],
       
    ];

  
    $currentAccountIndex = floor($offset / 190) % count($smtpAccounts);
    $currentAccount = $smtpAccounts[$currentAccountIndex];

    try {
        $mail->isSMTP();
        $mail->Host       = $currentAccount['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $currentAccount['username']; 
        $mail->Password   = $currentAccount['password'];          
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 465;
        $mail->setFrom($currentAccount['from'], 'УЦ АТБ');
        $mail->Subject    = 'Уведомление о низкой оценке';
    } catch (Exception $e) {
        die("Ошибка настройки PHPMailer: " . $mail->ErrorInfo);
    }

    $batchSize = 20; 
    $counter = 0;   
    $batchDelay = 5; 

    while ($row = $result9->fetch_assoc()) {
        $email = $row['email'];
        $test_name = $row['test_name'];

        try {
            $mail->addAddress($email);
            
            $mail->Body = "Уважаемый сотрудник,\n\nИнформируем Вас, о неудовлетворительном результате прохождения тестирования на компьютерном тренажере: $test_name.\n\nС целью подтверждения квалификации необходимо пересдать тест/экзамен в учебных классах на производстве или аудиториях Учебного Центра АТБ.";

            $mail->send();
            $mail->clearAddresses(); 

            echo "Письмо успешно отправлено на $email с тренажером $test_name<br>";
            $counter++;

            
            if ($counter == $batchSize) {
                echo "Делаем паузу на $batchDelay секунд...<br>";
                sleep($batchDelay); 
                $counter = 0; 
            }

        } catch (Exception $e) {
            echo "Ошибка при отправке письма на $email: " . $mail->ErrorInfo . "<br>";
        }
    }

    echo "Отправлено $limit писем. <br>";


    if ($result9->num_rows == $limit) {
        $nextOffset = $offset + $limit;
        echo "<a href='?offset=$nextOffset'>Отправить следующие $limit писем</a>";
    } else {
        echo "Все письма отправлены.";
    }
} else {
    echo "Нет данных для отправки уведомлений.";
}

$conn1->close();
