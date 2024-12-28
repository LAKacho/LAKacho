<?php

/*
 * Форма обратной связи (https://itchief.ru/lessons/php/feedback-form-for-website)
 * Copyright 2016-2020 Alexander Maltsev
 * Licensed under MIT (https://github.com/itchief/feedback-form/blob/master/LICENSE)
 */

header('Content-Type: application/json');

// обработка только ajax запросов (при других запросах завершаем выполнение скрипта)
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
  exit();
}

// обработка данных, посланных только методом POST (при остальных методах завершаем выполнение скрипта)
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  exit();
}

/* 1 ЭТАП - НАСТРОЙКА ПЕРЕМЕННЫХ */

const
IS_CHECK_CAPTCHA = true, // проверять капчу
IS_SEND_MAIL = true, // отправлять письмо получателю
IS_SEND_MAIL_SENDER = false, // отправлять информационное письмо отправителю
IS_WRITE_LOG = true, // записывать данные в лог
UPLOAD_NAME = 'uploads', // имя директории для загрузки файлов
IS_SEND_FILES_IN_BODY = true, // добавить ссылки на файлы в тело письма
IS_SENS_FILES_AS_ATTACHMENTS = true, // необходимо ли прикреплять файлы к письму
MAX_FILE_SIZE = 524288, // максимальный размер файла (в байтах)
ALLOWED_EXTENSIONS = array('jpg', 'jpeg', 'bmp', 'gif', 'png'), // разрешённые расширения файлов
MAIL_FROM = 'admin@educationalcenter.aeromash.ru', // от какого email будет отправляться письмо
MAIL_FROM_NAME = 'educationalcenter.aeromash.ru', // от какого имени будет отправляться письмо
MAIL_SUBJECT = 'Сообщение с формы обратной связи для С.А. Карнюхина и А.В. Карпова', // тема письма
MAIL_ADDRESS = 'sa.karnyukhin@svo.aero, serghome@gmail.com', // кому необходимо отправить письмо
MAIL_SUBJECT_CLIENT = 'Ваше сообщение доставлено', // настройки mail для информирования пользователя о доставке сообщения
IS_SENDING_MAIL_VIA_SMTP = true, // выполнять отправку писем через SMTP
MAIL_SMTP_HOST = 'ssl://smtp.yandex.ru', // SMTP-хост
MAIL_SMTP_PORT = '465', // SMTP-порт
MAIL_SMTP_USERNAME = 'admin@educationalcenter.aeromash.ru', // SMTP-пользователь
MAIL_SMTP_PASSWORD = 'YN48qMc1'; // SMTP-пароль
$uploadPath = dirname(dirname(__FILE__)) . '/' . UPLOAD_NAME . '/'; // директория для хранения загруженных файлов
$startPath = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/';

function log_write($message)
{
  if (IS_WRITE_LOG === false) {
    return;
  }
  $output = date('d.m.Y H:i:s') . PHP_EOL . $message . PHP_EOL . '-------------------------' . PHP_EOL;
  file_put_contents(dirname(dirname(__FILE__)) . './logs/logs.txt', $output, FILE_APPEND | LOCK_EX);
}

function message_write($message, $nameFail)
{
  if (IS_WRITE_LOG === false) {
    return;
  }
  $output = date('d.m.Y H:i:s') . PHP_EOL . $message . PHP_EOL . '-------------------------' . PHP_EOL;
  file_put_contents(dirname(dirname(__FILE__)) . './logs/'.$nameFail, $output, FILE_APPEND | LOCK_EX);
}


/* 2 ЭТАП - ПОДКЛЮЧЕНИЕ PHPMAILER */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('../phpmailer/src/Exception.php');
require_once('../phpmailer/src/PHPMailer.php');
require_once('../phpmailer/src/SMTP.php');

/* 3 ЭТАП - ОТКРЫТИЕ СЕССИИ И ИНИЦИАЛИЗАЦИЯ ПЕРЕМЕННОЙ ДЛЯ ХРАНЕНИЯ РЕЗУЛЬТАТОВ ОБРАБОТКИ ФОРМЫ */

session_start();
$data['result'] = 'success';

/* 4 ЭТАП - ВАЛИДАЦИЯ ДАННЫХ (ЗНАЧЕНИЙ ПОЛЕЙ ФОРМЫ) */

// проверка поля name (оно должно быть обязательно заполнено и иметь длину в диапазоне от 2 до 30 символов)
/*if (isset($_POST['name'])) {
  $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING); // защита от XSS
  $nameLength = mb_strlen($name, 'UTF-8');
  if ($nameLength < 2) {
    $data['name'] = 'Текст должен быть не короче 2 симв. Длина текста сейчас: ' . $nameLength . ' симв.';
    $data['result'] = 'error';
    log_write('Не пройдена валидация поля: name! Его длина равна ' . $nameLength . ' симв.');
  } else if ($nameLength > 130) {
    $data['name'] = 'Длина текста не должна превышать 130 симв. (сейчас ' . $nameLength . ' симв.).';
    $data['result'] = 'error';
    log_write('Не пройдена валидация поля: name! Его длина равна ' . $nameLength . ' симв.');
  }
} else {
  $data['name'] = 'Заполните это поле.';
  $data['result'] = 'error';
  log_write('Не пройдена валидация поля: name! Оно не заполнено!');
}*/


/*if (isset($_POST['texts'])){
 if (!is_numeric($_POST['texts']))
 {
$data['texts'] = 'Не корретный табельный номер.';
$data['result'] = 'error';
 } else  {
  $tabNomer= $_POST['texts'];
 }

} else 
  {
$data['texts'] = 'Заполните это поле.';
$data['result'] = 'error';
}*/

$tabNomer=$_COOKIE["idlip"];
$tabNomer=substr($tabNomer, 1);
$name = $_COOKIE["idnames"];


function checkPhoneNumber($phoneNumber)
{
	
	$phoneNumber = preg_replace('/\s|\+|-|\(|\)/','', $phoneNumber); // удалим пробелы, и прочие не нужные знаки is_numeric
	
	if(is_numeric($phoneNumber))
	{
		if(strlen($phoneNumber) < 5) // если длина номера слишком короткая, вернем false 
		{
			return FALSE;
		}
		else
		{
			return $phoneNumber;			
		}
	}
	else
	{
		return FALSE;
	}
}
 if (!filter_var($_POST['where'], FILTER_SANITIZE_STRING)){$data['result'] = 'error';}   




//проверка поля telefon (оно должно присутствовать и иметь корректное значение)
if (isset($_POST['tels'])) {
   if (checkPhoneNumber($_POST['tels']))
  { // защита от XSS
    $tels = checkPhoneNumber($_POST['tels']);
    $tels = strval($tels);
    if ($tels[0]==7||$tels[0]==8) {$tels=substr_replace($tels, '+7',0,1);} else
    {  
    $data['tels'] = 'телефон не корректный (1 знак)';
    $data['result'] = 'error';
    log_write('Не пройдена валидация поля: телефон! Оно имеет не корретное значение!');  }
  	
   	if(strlen($tels)!==12)
    {
    $data['tels'] = 'телефон не корректный (количество знаков)';
    $data['result'] = 'error';
    log_write('Не пройдена валидация поля: телефон! Оно имеет не корретное значение!');
    }   
   
   } else {
    $data['tels'] = 'телефон не корректный';
    $data['result'] = 'error';
    log_write('Не пройдена валидация поля: телефон! Оно имеет не корретное значение!'); 
  }
} else {
  $data['email'] = 'Заполните это поле.';
  $data['result'] = 'error';
  log_write('Не пройдена валидация поля: телефон! Оно не заполнено!');
}



//проверка поля email (если присутствовать и иметь корректное значение)
if (isset($_POST['email'])&&$_POST['email']!="") {
  if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) { // защита от XSS
    $data['email'] = 'Адрес электронной почты не корректный';
    $data['result'] = 'error';
    log_write('Не пройдена валидация поля: email! Оно имеет не корретное значение!');
  } else {
    $email = $_POST['email'];
  }
} else {
  //$data['email'] = 'Заполните это поле.';
  //$data['result'] = 'error';
  //log_write('Не пройдена валидация поля: email! Оно не заполнено!');
}

if (isset($_POST['podr']))
{
$podr = filter_var($_POST['podr'], FILTER_SANITIZE_STRING);  

}

if (isset($_POST['data1'])&&isset($_POST['data2']))
{
$data1 = filter_var($_POST['data1'], FILTER_SANITIZE_STRING);  
$exp=explode("-",$data1);
$data1=$exp[0];

$data2 = filter_var($_POST['data2'], FILTER_SANITIZE_STRING);  
$exp=explode("-",$data2);
$data2=$exp[0];

$data1 = (int)$data1;
$data2 = (int)$data2;
$year=$data2-$data1-1;
$between1=$data2;
if  ($year<-1){  $data['data2'] = '';
    			 $data['result'] = 'error';}else{ 
while ($year>-1)
{
$between = ($data1+$year);	
//$between1 = (string)$between;
$between1 .=", " . $between;
$year--;


}}
 $data2 = $between1;
}




// проверка поля message (это поле должно присутствовать и иметь длину в диапазоне от 20 до 500 символов)
if (isset($_POST['message'])) {
  $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING); // защита от XSS
  $messageLength = mb_strlen($message, 'UTF-8');
  if ($messageLength < 0) {
    $data['message'] = 'Текст должен быть не короче 0 симв. Длина текста сейчас: ' . $messageLength . ' симв.';
    $data['result'] = 'error';
    log_write('Не пройдена валидация поля: message! Его длина равна ' . $messageLength . ' симв.');
  } else if ($messageLength > 500) {
    $data['message'] = 'Длина текста не должна превышать 500 симв. (сейчас ' . $messageLength . ' симв.)';
    $data['result'] = 'error';
    log_write('Не пройдена валидация поля: message! Его длина равна ' . $messageLength . ' симв.');
  }
} else {
  //$data['message'] = 'Заполните это поле.';
  //$data['result'] = 'error';
}

if (isset($_POST['message1'])) {
  $message1 = filter_var($_POST['message1'], FILTER_SANITIZE_STRING); // защита от XSS
  $message1Length = mb_strlen($message1, 'UTF-8');
  if ($message1Length < 0) {
    $data['message'] = 'Текст должен быть не короче 0 симв. Длина текста сейчас: ' . $message1Length . ' симв.';
    $data['result'] = 'error';
    log_write('Не пройдена валидация поля: message! Его длина равна ' . $message1Length . ' симв.');
  } else if ($message1Length > 500) {
    $data['message'] = 'Длина текста не должна превышать 500 симв. (сейчас ' . $message1Length . ' симв.)';
    $data['result'] = 'error';
    log_write('Не пройдена валидация поля: message! Его длина равна ' . $message1Length . ' симв.');
  }
} 

/* 5 ЭТАП - ПРОВЕРКА КАПЧИ */
if (IS_CHECK_CAPTCHA == true) {
  if (isset($_POST['captcha']) && isset($_SESSION['captcha'])) {
    $captcha = filter_var($_POST['captcha'], FILTER_SANITIZE_STRING); // защита от XSS
    if ($_SESSION['captcha'] != $captcha) { // проверка капчи
      $data['captcha'] = 'Код не соответствует изображению.';
      $data['result'] = 'error';
      log_write('Не пройдена валидация поля: captcha! Указанный код ' . $captcha . ' не соответствует сгенерированному на сервере ' . $_SESSION['captcha']);
    }
  } else {
    $data['captcha'] = 'Ошибка при проверке кода.';
    $data['result'] = 'error';
    log_write('Произошла ошибка при проверке капчи!');
  }
}
// проверям какие справки нужны
$reference = "";
$referenceOk = "";
$referenceSec = "";
$referenceEc = "";

if (isset($_POST['2HDFL+'])&&$_POST['2HDFL+']=="on") 
{ 
 $reference =  'при увольнении (пакет 2-НДФЛ + 182-Н)';
  
}

if (isset($_POST['2HDFL'])&&$_POST['2HDFL']=="on") 
{ 
 
  if($reference=="") {$reference .=  '2-НДФЛ';}
  
}

 if (isset($_POST['exchange'])&&$_POST['exchange']=="on")

 {  if($reference=="") {$reference .=  'на биржу';}
 else  {$reference .=  ', на биржу'; } } 
  
 // справка свободного образца
if (isset($_POST['reference'])&&$_POST['reference']=="on") {
   if (isset($_POST['message'])&&$_POST['message']=="") {
  
  $data['message'] = 'Заполните это поле.';
  $data['result'] = 'error';
  log_write('Не пройдена валидация поля: message! Оно не заполнено!');
  } else { if ($reference==""){ $reference .= $_POST['message'];} 
         	else { $reference .= ", " . $_POST['message'];  }}}
  
//--------------учебный центр----------------
  

  
    if (isset($_POST['ab1'])&&$_POST['ab1']=="on")
	 {  $referenceEc =  'АБ'; } 
 	if (isset($_POST['tb1'])&&$_POST['tb1']=="on")
	 {  $referenceEc =  'ТБ'; } 
 	if (isset($_POST['tb1'])&&isset($_POST['ab1'])&&$_POST['tb1']=="on"&&$_POST['ab1']=="on")
	 {  $referenceEc =  'ТБ и АБ'; } 


//--------------в СБ----------------
$referenceSecPers="";
$referenceSec="";
if (isset($_POST['interview'])&&$_POST['interview']=="on") 
{ 
 $referenceSec =  'Заявка на командировку в АРЕ';
  
}

if (isset($_POST['personnel'])&&$_POST['personnel']=="on") 
{ 
 $referenceSecPers =  'Заявка в кадровый резерв';
  
}


//--------------в отдел кадров----------------

if (isset($_POST['copyTK'])&&$_POST['copyTK']=="on") 
{ 
 $referenceOk =  'Копия трудовой книжки';
  
}

if (isset($_POST['refMR'])&&$_POST['refMR']=="on")

 {  if($referenceOk=="") {$referenceOk .=  'Cправка с места работы';}
 else  {$referenceOk .=  ', справка с места работы'; } } 

if (isset($_POST['doy'])&&$_POST['doy']=="on")

 {  if($referenceOk=="") {$referenceOk .=  'справка в ДОУ о нахождение работника в отпуске';}
 else  {$referenceOk .=  ', справка в ДОУ о нахождение работника в отпуске'; } }   
  
 
if (isset($_POST['std-r'])&&$_POST['std-r']=="on")

 {  if($referenceOk=="") {$referenceOk .=  'Справка СТД-Р';}
 else  {$referenceOk .=  ', справка СТД-Р'; } } 

if (isset($_POST['order'])&&$_POST['order']=="on") {
   if (isset($_POST['message1'])&&$_POST['message1']=="") {
  
  $data['message1'] = 'Заполните это поле.';
  $data['result'] = 'error';
  log_write('Не пройдена валидация поля: message1! Оно не заполнено!');
  } else { if ($referenceOk==""){ $referenceOk .= $_POST['message1']; } 
         	else { $referenceOk .= ", " . $_POST['message1']; }}}



//else   { 
 //if($reference=="") {$reference .=  $_POST['message']);}
 //else  {$reference .= $_POST['message']);} } 
  
  
/* 6 ЭТАП - ВАЛИДАЦИЯ ФАЙЛОВ 
if (isset($_FILES['attachment'])) {
  // перебор массива $_FILES['attachment']
  foreach ($_FILES['attachment']['error'] as $key => $error) {
    // если файл был успешно загружен на сервер (ошибок не возникло), то...
    if ($error == UPLOAD_ERR_OK) {
      // получаем имя файла
      $fileName = $_FILES['attachment']['name'][$key];
      // получаем расширение файла в нижнем регистре
      $fileExtension = mb_strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
      // получаем размер файла
      $fileSize = $_FILES['attachment']['size'][$key];
      // результат проверки расширения файла
      $resultCheckExtension = true;
      // проверяем расширение загруженного файла
      if (!in_array($fileExtension, ALLOWED_EXTENSIONS)) {
        $resultCheckExtension = false;
        $data['attachment'][$key] = 'Файл имеет не разрешённый тип.';
        $data['result'] = 'error';
        log_write('Произошла ошибка! Файл ' . $fileName . ' имеет не разрешённый тип.');
      }
      // проверяем размер файла
      if ($resultCheckExtension && ($fileSize > MAX_FILE_SIZE)) {
        $data['attachment'][$key] = 'Размер файла превышает допустимый.';
        $data['result'] = 'error';
        log_write('Произошла ошибка! Файл ' . $fileName . ' имеет не разрешённый размер.');
      }
    } else {
      $data['attachment'][$key] = 'Ошибка при загрузке файла.';
      $data['result'] = 'error';
      log_write('Произошла ошибка при загрузке файла на сервер!');
    }
  }
  // если ошибок валидации не возникло, то переместим файл в директорию $uploadPath
  if ($data['result'] == 'success') {
    // переменная для хранения имён файлов
    $attachments = array();
    // перемещение файлов в директорию $uploadPath
    foreach ($_FILES['attachment']['name'] as $key => $attachment) {
      // получаем имя файла
      $fileName = basename($_FILES['attachment']['name'][$key]);
      // получаем расширение файла в нижнем регистре
      $fileExtension = mb_strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
      // временное имя файла на сервере
      $fileTmp = $_FILES['attachment']['tmp_name'][$key];
      // создаём уникальное имя
      $fileNewName = uniqid('upload_', true) . '.' . $fileExtension;
      // перемещаем файл в директорию
      if (!move_uploaded_file($fileTmp, $uploadPath . $fileNewName)) {
        // ошибка при перемещении файла
        $data['attachment'][$key] = 'Ошибка при загрузке файла.';
        $data['result'] = 'error';
        log_write('Произошла ошибка при перемещении файла в директорию, определяемою переменной $uploadPath!');
      } else {
        $attachments[] = $uploadPath . $fileNewName;
      }
    }
  }
}

/* 7 ЭТАП - ОТПРАВКА ПИСЬМА ПОЛУЧАТЕЛЮ 
/*if ($data['result'] == 'success' && IS_SEND_MAIL == true) {
  try {
    // получаем содержимое email шаблона
    $bodyMail = file_get_contents('email.tpl');
    // выполняем замену плейсхолдеров реальными значениями
    $bodyMail = str_replace('%email.title%', MAIL_SUBJECT, $bodyMail);
    $bodyMail = str_replace('%email.nameuser%', isset($name) ? $name : '-', $bodyMail);
    $bodyMail = str_replace('%email.message%', isset($message) ? $message : '-', $bodyMail);
    $bodyMail = str_replace('%email.emailuser%', isset($email) ? $email : '-', $bodyMail);
    $bodyMail = str_replace('%email.date%', date('d.m.Y H:i'), $bodyMail);
    // добавление файлов в виде ссылок
    if (IS_SEND_FILES_IN_BODY) {
      if (isset($attachments)) {
        $listFiles = '<ul>';
        foreach ($attachments as $attachment) {
          $fileHref = substr($attachment, strpos($attachment, basename(dirname(__DIR__)) . '/' . UPLOAD_NAME . '/'));
          $fileName = basename($fileHref);
          $listFiles .= '<li><a href="' . $startPath . $fileHref . '">' . $fileName . '</a></li>';
        }
        $listFiles .= '</ul>';
        $bodyMail = str_replace('%email.attachments%', $listFiles, $bodyMail);
      } else {
        $bodyMail = str_replace('%email.attachments%', '-', $bodyMail);
      }
    }
    // устанавливаем параметры
    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';

    // Отправка письма по SMTP 
    if (IS_SENDING_MAIL_VIA_SMTP === true) {
      $mail->isSMTP();
      $mail->SMTPAuth = true;
      $mail->Host = MAIL_SMTP_HOST;
      $mail->Port = MAIL_SMTP_PORT;
      $mail->Username = MAIL_SMTP_USERNAME;
      $mail->Password = MAIL_SMTP_PASSWORD;
    }

    $mail->Encoding = 'base64';
    $mail->IsHTML(true);
    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->Subject = MAIL_SUBJECT;
    $mail->Body = $bodyMail;

    $emails = explode(',', MAIL_ADDRESS);
    foreach ($emails as $address) {
      $mail->addAddress(trim($address));
    }

    // прикрепление файлов к письму
    if (IS_SENS_FILES_AS_ATTACHMENTS) {
      if (isset($attachments)) {
        foreach ($attachments as $attachment) {
          $mail->addAttachment($attachment);
        }
      }
    }
    // отправляем письмо
    if (!$mail->send()) {
      $data['result'] = 'error';
      log_write('Ошибка при отправке письма: ' . $mail->ErrorInfo);
    }
  } catch (Exception $e) {
    log_write('Ошибка: ' . $e->getMessage());
  }
}*/

  
  
  
/* 8 ЭТАП - ОТПРАВКА ИНФОРМАЦИОННОГО ПИСЬМА ОТПРАВИТЕЛЮ */
if ($data['result'] == 'success' && IS_SEND_MAIL_SENDER == true) {
  try {
    // очистка всех адресов и прикреплёных файлов
    $mail->clearAllRecipients();
    $mail->clearAttachments();
    // получаем содержимое email шаблона
    $bodyMail = file_get_contents('email_client.tpl');
    // выполняем замену плейсхолдеров реальными значениями
    $bodyMail = str_replace('%email.title%', MAIL_SUBJECT, $bodyMail);
    $bodyMail = str_replace('%email.nameuser%', isset($name) ? $name : '-', $bodyMail);
    $bodyMail = str_replace('%email.date%', date('d.m.Y H:i'), $bodyMail);
    // устанавливаем параметры
    $mail->Subject = MAIL_SUBJECT_CLIENT;
    $mail->Body = $bodyMail;
    $mail->addAddress($email);
    // отправляем письмо
    if (!$mail->send()) {
      $data['result'] = 'error';
      log_write('Ошибка при отправке письма: ' . $mail->ErrorInfo);
    }
  } catch (Exception $e) {
    log_write('Ошибка: ' . $e->getMessage());
  }
}

/* 9 ЭТАП - ЗАПИСЫВАЕМ ДАННЫЕ В ЛОГ */
if ($data['result'] == 'success' && IS_WRITE_LOG) {
  $output = 'Подразделение: ' . (isset($podr) ? $podr : '-') . PHP_EOL;
  $output .= 'ФИО: ' . (isset($name) ? $name : '-') . PHP_EOL;
  $output .= 'ТБ: ' . (isset($tabNomer) ? $tabNomer : '-') . PHP_EOL;
  $output .= 'Доставка: ' .$_POST['where']. PHP_EOL;
  $output .= 'Телефон: ' . (isset($tels) ? $tels : '-') . PHP_EOL;
  $output .= 'Адрес email: ' . (isset($email) ? $email : '-') . PHP_EOL;
  
  $outputPd .= 'Справки: ' . (isset($reference) ? $reference : '-') . PHP_EOL;
  //$outputPd .= 'НДФЛ за: ' . (isset($data1) ? $data1 : '-') . PHP_EOL;
  $outputPd .= 'НДФЛ за: ' . (isset($data2) ? $data2 : '-') . PHP_EOL;
  $outputPd .= 'Справка на ОБ: ' . (isset($message) ? $message : '-') . PHP_EOL;
  
  $outputOk .= 'Справки: ' . (isset($referenceOk) ? $referenceOk : '-') . PHP_EOL;
  $outputOk .= 'Выписка: ' . (isset($message1) ? $message1 : '-') . PHP_EOL;
  
  $outputEc = 'Сертификат: по ' . (isset($referenceEc) ? $referenceEc : '-') . PHP_EOL;
 
  $outputSec = (isset($referenceSec) ? $referenceSec : '-') . PHP_EOL; 
  $outputSecPers = (isset($referenceSecPers) ? $referenceSecPers : '-') . PHP_EOL;
  
  log_write('Письмо успешно отправлено!' . PHP_EOL . $output . $outputPd . $outputEc. $outputOk. $outputSec. $outputSecPers);
  
  if ($reference!=="")    
  {message_write('Письмо успешно отправлено!' . PHP_EOL . $output . $outputPd, "logs1.txt");}
  
  if ($referenceEc!=="")    
  {message_write('Письмо успешно отправлено!' . PHP_EOL . $output . $outputEc, "logs2.txt");}
  
  if ($referenceOk!=="")    
  {message_write('Письмо успешно отправлено!' . PHP_EOL . $output . $outputOk, "logs3.txt");}

  if ($referenceSec!=="")    
  {message_write('Письмо успешно отправлено!' . PHP_EOL . $output . $outputSec, "logSec.txt");}

	 if ($referenceSecPers!=="")    
  {message_write('Письмо успешно отправлено!' . PHP_EOL . $output . $referenceSecPers, "logSec.txt");}
}

/* ФИНАЛЬНЫЙ ЭТАП - ВОЗВРАЩАЕМ РЕЗУЛЬТАТЫ РАБОТЫ В ФОРМАТЕ JSON $referenceSecPers*/
echo json_encode($data);
