<?php 

require 'vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$conn1 = new mysql("localhost","u159215", "","b159215_intern"); //данный куда все будет записываться и мыло 
if ($conn1-> connect_error) {
	die("Соеденения нет1" . $conn1->connect_error);
	}
	
$conn2 = new mysql("localhost","u159215", "","1test_system");// откуда все будет браться оценки  
if ($conn2->connect_error) {
	die ("Соеденения нет2" . $conn2-> connect_error);
}
$sql1 = "SELECT user_login
		FROM xtvr_sesions24 
		WHERE lesson_name IN (303, 305) AND lesson_result <= 72 AND time_in >= CURDATE() - INTERVAL 7 DAY"; // Двойки берем за неделю
$result1 = $conn2->query($sql1);
if ($result->num_rows > 0) {
	$conn1->query("TRUNCETE TABLE dva"); // удаляем все за прошлую неделю
	
	
	 $mail = new PHPMailer(true);
	 
	 try { 
		$mail->isSMTP();
		$mail->Host = 'ssl://smtp.yandex.ru';
		$mail->SMTPAuth = true;
		$mail->Username = '';
		$mail->Password = '';
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		$mail->Post = 465; 
		$mail->setFrom(''.'УЦ АТБ'); 
		$mail->SUBject= 'Увидомления о низкой оценк';// Данные от емайла 
		
		while ($row = $result->fetch_assoc()) {
			$login = $row['tb_nomer'];
			
			$sql2="SELECT email from intern_all WHERE tb_nomer = ?";// Почты берем
            $stmt = $conn1->prepare($sql2);
            $stmt->bind_param("i", $login);
            $stmt->execute();
            $stmt->bind_result($email);
            $stmt->fetch();
            $stmt->close();

            if ($email) {
                $insert_sql = "INSERT INTO dva (login, email, grade) VALUES (?, ?, ?)";// записываем в базу 
                $insert_stmt = $conn1->prepare($insert_sql);
                $grade = 2;
                $insert_stmt->bind_param("isi", $login, $email, $grade);
                $insert_stmt->execute();
                $insert_stmt->close();

                $mail->addAddress($email); // отправляем уведомления
                $mail->Body = "Уважаемый сотрудник";// текс придумай 
                $mail->send();
                $mail->clearAddresses();
            }
        }
    } catch (Exception $e) {
        echo "Ошибка при отправке письма: {$mail->ErrorInfo}";
    }

    echo "Данные успешно обновлены и уведомления отправлены.";
} else {
    echo "Нет студентов с оценкой 2 за неделю.";
}

$conn1->close();
$conn2->close();
?>
