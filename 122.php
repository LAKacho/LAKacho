<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn1 = new mysqli("localhost", "u159215", "", "1test_system");
if ($conn1->connect_error) {
    die("Ошибка соединения с базой 1: " . $conn1->connect_error);
}

$conn2 = new mysqli("localhost", "u159215", "","intern_shB");
if ($conn2->connect_error) {
    die("Ошибка соединения с базой 2: " . $conn2->connect_error);
}

$conn3 = new mysqli("localhost", "u159215", "", "123");
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

            echo "Login $login успешно записан в таблицу dva xtvs.<br>";
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

            echo "Login $login успешно записан в таблицу dva РКМ.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}

$sql5 = "SELECT user_login
         FROM sess_dpk
         WHERE error2 = 2
           AND times >= CURDATE() - INTERVAL 7 DAY
           AND level2 = 'test_dpk'" ;

$result5 = $conn3->query($sql5);
if (!$result5) {
    die("Ошибка выполнения запроса: " . $conn3->error);
}

if ($result5->num_rows > 0) {
    while ($row = $result5->fetch_assoc()) {
        $login = $row['user_login'];

        $sql6 = "SELECT email FROM list WHERE tb = ?";
        $stmt = $conn1->prepare($sql6);
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
            $test_name = "T.A.P";
            $insert_stmt->bind_param("ssis", $login, $email, $grade, $test_name);
            $insert_stmt->execute();
            $insert_stmt->close();

            echo "Login $login успешно записан в таблицу dva ПАПК.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}



$sql7 = "SELECT user_login
         FROM sess_sr
         WHERE error2 = 2
           AND times >= CURDATE() - INTERVAL 7 DAY
           AND level2 = 'test'" ;

$result6 = $conn3->query($sql7);
if (!$result6) {
    die("Ошибка выполнения запроса: " . $conn3->error);
}

if ($result6->num_rows > 0) {
    while ($row = $result6->fetch_assoc()) {
        $login = $row['user_login'];

        $sql8 = "SELECT email FROM list WHERE tb = ?";
        $stmt = $conn1->prepare($sql8);
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
            $test_name = "P.A.S.R";
            $insert_stmt->bind_param("ssis", $login, $email, $grade, $test_name);
            $insert_stmt->execute();
            $insert_stmt->close();

            echo "Login $login успешно записан в таблицу dva Паср.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}



$sql9 = "SELECT user_login
         FROM sess1
         WHERE error2 = 2
           AND times >= CURDATE() - INTERVAL 7 DAY
           AND level2 = 5" ;

$result7 = $conn3->query($sql9);
if (!$result6) {
    die("Ошибка выполнения запроса: " . $conn3->error);
}

if ($result7->num_rows > 0) {
    while ($row = $result7->fetch_assoc()) {
        $login = $row['user_login'];

        $sql10 = "SELECT email FROM list WHERE tb = ?";
        $stmt = $conn1->prepare($sql10);
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
            $test_name = "DSM";
            $insert_stmt->bind_param("ssis", $login, $email, $grade, $test_name);
            $insert_stmt->execute();
            $insert_stmt->close();

            echo "Login $login успешно записан в таблицу dva DSM.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}

  $sql11 = "SELECT login, email, test_name FROM dva";
$result9 = $conn1->query($sql11);

if ($result9->num_rows > 0) {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    try {
        $mail->isSMTP();
        $mail->Host       = 'ssl://smtp.yandex.ru'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'metyolckin.c@yandex.ru'; 
        $mail->Password   = 'tcxqiebivfofroup';          
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 465;
        $mail->setFrom('metyolckin.c@yandex.ru', 'УЦ АТБ');
        $mail->Subject    = 'Уведомление о низкой оценке';
    } catch (Exception $e) {
        die("Ошибка настройки PHPMailer: " . $mail->ErrorInfo);
    }

    while ($row = $result9->fetch_assoc()) {
        $email = $row['email'];
        $test_name = $row['test_name'];

        try {
            $mail->addAddress($email);
            
            $mail->Body = "Уважаемый сотрудник,\n\nИнформируем Вас, о неудовлетворительном  результате прохождения  тестирования на компьютерном тренажер: $test_name.\n\nС целью подтверждения квалификации необходимо пересдать тест\экзамен  в  учебных классах на производстве или  аудиториях Учебного Центра АТБ.";

            $mail->send();
            $mail->clearAddresses(); 

            echo "Письмо успешно отправлено на $email с тренажер $test_name<br>";
        } catch (Exception $e) {
            echo "Ошибка при отправке письма на $email: " . $mail->ErrorInfo . "<br>";
        }
    }
} else {
    echo "Нет данных для отправки уведомлений.";
}


echo "Данные успешно обновлены.";

$conn1->close();
$conn2->close();
$conn3->close();
?>
