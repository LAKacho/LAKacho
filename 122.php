<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Подключение к базе данных 1 (для записи данных и получения email)
$conn1 = new mysqli("localhost", "u159215", "", "b159215_intern");
if ($conn1->connect_error) {
    die("Ошибка соединения с базой 1: " . $conn1->connect_error);
}

// Подключение к базе данных 2 (для получения оценок)
$conn2 = new mysqli("localhost", "u159215", "", "1test_system");
if ($conn2->connect_error) {
    die("Ошибка соединения с базой 2: " . $conn2->connect_error);
}

// Подключение к базе данных 3 (для получения оценок из других таблиц)
$conn3 = new mysqli("localhost", "u159215", "", "123");
if ($conn3->connect_error) {
    die("Ошибка соединения с базой 3: " . $conn3->connect_error);
}

// Функция для обработки результатов и записи в таблицу `dva`
function process_grades($result, $conn1, $test_name) {
    while ($row = $result->fetch_assoc()) {
        $login = $row['user_login'];

        // Получение email по user_login из таблицы list
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
            // Запись в таблицу dva
            $insert_sql = "INSERT INTO dva (login, email, grade, test_name) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn1->prepare($insert_sql);
            if (!$insert_stmt) {
                echo "Ошибка подготовки запроса вставки для login = $login: " . $conn1->error;
                continue;
            }
            $grade = 2;
            $insert_stmt->bind_param("isis", $login, $email, $grade, $test_name);
            $insert_stmt->execute();
            $insert_stmt->close();
        } else {
            echo "Email для пользователя с login = $login не найден.<br>";
        }
    }
}

// Очищаем таблицу 'dva' перед новой записью
if (!$conn1->query("TRUNCATE TABLE dva")) {
    die("Ошибка очистки таблицы 'dva': " . $conn1->error);
}

// Запросы для базы данных 2 (xtvr_sesions24)
$sql1 = "SELECT user_login, lesson_name FROM xtvr_sesions24
         WHERE lesson_result = 2 AND time_in >= CURDATE() - INTERVAL 7 DAY";
$result1 = $conn2->query($sql1);
if (!$result1) {
    die("Ошибка выполнения запроса к базе 2: " . $conn2->error);
}
if ($result1->num_rows > 0) {
    process_grades($result1, $conn1, "Тест из базы 2 (xtvr_sesions24)");
}

// Запросы для таблиц в базе данных 3
// 1. Таблица rkm
$sql2 = "SELECT user_login, test_name_column FROM rkm WHERE grade_rkm = 2 AND date >= CURDATE() - INTERVAL 7 DAY";
$result2 = $conn3->query($sql2);
if ($result2->num_rows > 0) {
    process_grades($result2, $conn1, "Тест из таблицы rkm");
}

// 2. Таблица sess1
$sql3 = "SELECT user_login, test_name_column FROM sess1 WHERE grade_sess1 = 2 AND date >= CURDATE() - INTERVAL 7 DAY";
$result3 = $conn3->query($sql3);
if ($result3->num_rows > 0) {
    process_grades($result3, $conn1, "Тест из таблицы sess1");
}

// 3. Таблица sess_dpk
$sql4 = "SELECT user_login, test_name_column FROM sess_dpk WHERE grade_sess_dpk = 2 AND date >= CURDATE() - INTERVAL 7 DAY";
$result4 = $conn3->query($sql4);
if ($result4->num_rows > 0) {
    process_grades($result4, $conn1, "Тест из таблицы sess_dpk");
}

// 4. Таблица sess_sr
$sql5 = "SELECT user_login, test_name_column FROM sess_sr WHERE grade_sess_sr = 2 AND date >= CURDATE() - INTERVAL 7 DAY";
$result5 = $conn3->query($sql5);
if ($result5->num_rows > 0) {
    process_grades($result5, $conn1, "Тест из таблицы sess_sr");
}

// 5. Таблица seas_tdvs_tren
$sql6 = "SELECT user_login, test_name_column FROM seas_tdvs_tren WHERE grade_seas_tdvs_tren = 2 AND date >= CURDATE() - INTERVAL 7 DAY";
$result6 = $conn3->query($sql6);
if ($result6->num_rows > 0) {
    process_grades($result6, $conn1, "Тест из таблицы seas_tdvs_tren");
}

// Закрываем подключения
$conn1->close();
$conn2->close();
$conn3->close();
?>