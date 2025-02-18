<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn1 = new mysqli("localhost", "root", "", "dsm-training");
if ($conn1->connect_error) {
    die("Ошибка соединения с базой 1: " . $conn1->connect_error);
}

$conn2 = new mysqli("localhost", "root", "","xtvr");
if ($conn2->connect_error) {
    die("Ошибка соединения с базой 2: " . $conn2->connect_error);
}

$conn3 = new mysqli("localhost", "root", "", "test");
if ($conn3->connect_error) {
    die("Ошибка соединения с базой 3: " . $conn3->connect_error);
}


$sql1 = "SELECT s.user_login
          FROM xtvr_sesions24 s
          INNER JOIN (
                SELECT 
                  user_login,
                  MAX(time_in) AS max_time
                FROM xtvr_sesions24
                WHERE time_in >= CURDATE() - INTERVAL 7 DAY
                GROUP BY user_login
                ) AS last_attampts 
                ON s.user_login = last_attampts.user_login
                AND s.time_in = last_attampts.max_time
                WHERE 
                  s.lesson_name IN (303,305) 
                  AND s.lesson_result <= 72";

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
            $insert_sql = "INSERT INTO dva (login, email, garade, test_name) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn1->prepare($insert_sql);
            if (!$insert_stmt) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $garade = 2;
            $test_name = "xtvs";
            $insert_stmt->bind_param("ssis", $login, $email, $garade, $test_name);
            $insert_stmt->execute();
            $insert_stmt->close();

            echo "Login $login успешно записан в таблицу dva xtvs.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}

$sql3 = "SELECT r.user_login
			FROM rkm r
			INNER JOIN (
				SELECT 
					user_login, 
					MAX(time_in) AS max_time
				FROM rkm 
				WHERE time_in >= CURDATE() - INTERVAL 7 DAY
				GROUP BY user_login)
				AS last_attampts 
				ON r.user_login = last_attampts.user_login
				AND r.time_in = last_attampts.max_time
				WHERE r.ocenka = 2 ";

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
            $insert_sql = "INSERT INTO dva (login, email, garade, test_name) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn1->prepare($insert_sql);
            if (!$insert_stmt) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $garade = 2;
            $test_name = "rkm";
            $insert_stmt->bind_param("ssis", $login, $email, $garade, $test_name);
            $insert_stmt->execute();
            $insert_stmt->close();

            echo "Login $login успешно записан в таблицу dva РКМ.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}

$sql5 = "SELECT s.user_login 
			FROM sess_dpk s
			INNER JOIN (
				SELECT 
					user_login, 
					MAX(times) AS max_time
				FROM sess_dpk 
				WHERE times >= CURDATE() - INTERVAL 7 DAY
				AND level2 = 'test_dpk'
				GROUP BY user_login)
				AS last_attampts 
				ON s.user_login = last_attampts.user_login
				AND s.times = last_attampts.max_time
				WHERE s.error = 2
				AND s.level2 = 'test_dpk'" ;

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
            $insert_sql = "INSERT INTO dva (login, email, garade, test_name) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn1->prepare($insert_sql);
            if (!$insert_stmt) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $garade = 2;
            $test_name = "T.A.P";
            $insert_stmt->bind_param("ssis", $login, $email, $garade, $test_name);
            $insert_stmt->execute();
            $insert_stmt->close();

            echo "Login $login успешно записан в таблицу dva ПАПК.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}



$sql7 = "	SELECT s.user_login 
			FROM sess_sr s
			INNER JOIN (
				SELECT 
					user_login, 
					MAX(times) AS max_time
				FROM sess_sr 
				WHERE times >= CURDATE() - INTERVAL 7 DAY
				AND level2 = 'test'
				GROUP BY user_login)
				AS last_attampts 
				ON s.user_login = last_attampts.user_login
				AND s.times = last_attampts.max_time
				WHERE s.error2 = 2
				AND s.level2 = 'test'" ;

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
            $insert_sql = "INSERT INTO dva (login, email, garade, test_name) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn1->prepare($insert_sql);
            if (!$insert_stmt) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $garade = 2;
            $test_name = "P.A.S.R";
            $insert_stmt->bind_param("ssis", $login, $email, $garade, $test_name);
            $insert_stmt->execute();
            $insert_stmt->close();

            echo "Login $login успешно записан в таблицу dva Паср.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}



$sql9 = "	SELECT s.user_login 
			FROM sess1 s
			INNER JOIN (
				SELECT 
					user_login, 
					MAX(times) AS max_time
				FROM sess1 
				WHERE times >= CURDATE() - INTERVAL 7 DAY
				AND level2 = '5'
				GROUP BY user_login)
				AS last_attampts 
				ON s.user_login = last_attampts.user_login
				AND s.times = last_attampts.max_time
				WHERE s.error2 = 2
				AND s.level2 = '5'"	;

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
            $insert_sql = "INSERT INTO dva (login, email, garade, test_name) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn1->prepare($insert_sql);
            if (!$insert_stmt) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $garade = 2;
            $test_name = "DSM";
            $insert_stmt->bind_param("ssis", $login, $email, $garade, $test_name);
            $insert_stmt->execute();
            $insert_stmt->close();

            echo "Login $login успешно записан в таблицу dva DSM.<br>";
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}
// Получение всех данных из таблицы 'dva'
$sql11 = "SELECT * FROM dva";
$result11 = $conn1->query($sql11);

if (!$result11) {
    die("Ошибка выполнения запроса: " . $conn1->error);
}

// Определение пути для сохранения CSV файла
$csvFilePath = '/path/to/folder/dva_data.csv';

// Открытие файла для записи
$file = fopen($csvFilePath, 'w');

if ($file === false) {
    die("Не удалось создать CSV файл.");
}

// Записываем заголовки в CSV файл (замените на реальные имена полей)
fputcsv($file, ['login', 'email', 'garade', 'test_name']);

// Записываем строки данных из таблицы 'dva'
while ($row = $result11->fetch_assoc()) {
    fputcsv($file, $row);
}

// Закрытие файла
fclose($file);

echo "CSV файл успешно создан и сохранен по пути: $csvFilePath.<br>";

// Закрытие соединений с базой данных
$conn1->close();
$conn2->close();
$conn3->close();

$conn1->close();
$conn2->close();
$conn3->close();
?>
