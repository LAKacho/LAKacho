<?php

// Страница авторизации
foreach($_COOKIE as $key => $value) setcookie($key, '', time() - 3600, '/');
// Функция для генерации случайной строки
function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
    }
    return $code;
}
 $hello = "";
 $login[0]="";
 $passw[0]="";
// Соединямся с БД 
$link=mysqli_connect("localhost", "root", "", "");
if(isset($_POST['login'])){
    $login = $_POST['login'];
    $passw = $_POST['password'];}

if($login[0]=="B"&&$passw[0]=="B")
{	
	$login = mb_substr($login,1);
 	$passw = mb_substr($passw,1);} else {$login=$passw=0; }

if(isset($_POST['login']))
{
    
	// Вытаскиваем из БД запись, у которой логин равняеться введенному
    $query = mysqli_query($link,"SELECT user_password FROM users_act WHERE user_login='".mysqli_real_escape_string($link,$login)."' LIMIT 1");
    $data = mysqli_fetch_assoc($query);
  
    // Сравниваем пароли
    if($data!==null&&$data['user_password'] === ($passw))
    {
        // Генерируем случайное число и шифруем его
        $hash = (generateCode(10));

        $insip = date('Y/m/d H:i:s');
		
		
        // Записываем в БД новый хеш авторизации и IP
        //mysqli_query($link, "UPDATE users SET user_hash='".$hash."' ".$insip." WHERE user_id='".$data['user_id']."'");
		//mysqli_query($link,"INSERT INTO sess SET sum='".$data['user_id']."', time='".$insip."'");
        // Ставим куки
        setcookie("id_tpa", $passw, time()+60*60*24*30, "/");
        //setcookie("hash", $hash, time()+60*60*24*30, "/", null, null, true); // httponly !!!
		//setcookie("DSMCookie", $data, time()+3600);
        // Переадресовываем браузер на страницу проверки нашего скрипта
		if (isset($_COOKIE['id_tpa'])) echo "Город: " . $_COOKIE["id_tpa"] . "<br>";
        header("Location: category.php"); exit(); 
    }
    else
    {	
		$hello = "Вы ввели неправильный логин/пароль"; 
		if ($data === null) { $hello = "Вы ввели неправильный логин/пароль";  }
    }
	
    $_POST = array();
	
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <!--[if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script><![endif]-->
    <title></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link href="style_xtvs.css" rel="stylesheet">
<style>
textarea[name="password"] {
  resize: none;
  -webkit-text-security: disc !important;
}
</style>

<head>

<SCRIPT LANGUAGE="JavaScript">
function setCookie (name, value, expires, path, domain, secure) {
      document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

function getCookie(name) {
	var cookie = " " + document.cookie;
	var search = " " + name + "=";
	var setStr = null;
	var offset = 0;
	var end = 0;
	if (cookie.length > 0) {
		offset = cookie.indexOf(search);
		if (offset != -1) {
			offset += search.length;
			end = cookie.indexOf(";", offset)
			if (end == -1) {
				end = cookie.length;
			}
			setStr = unescape(cookie.substring(offset, end));
		}
	}
	return(setStr);
}


</SCRIPT>
</head>

<body>

<form id="login" method="POST" autocomplete="false"> <img id="pro" src="4.jpg" width="187px" style=" 
            alt=foto" >
    <h1>Тренажер</h1> <h2>аттестации  </h2><h2> по ТБ</h2>
	
    <fieldset id="inputs">
        <input id="username" type="text" name="login" placeholder="логин" autocomplete="off" autofocus required>   
        <input id="password" type="password" name="password" autocomplete="new-password" autocomplete="off" placeholder="пароль" required>
    </fieldset>
    <fieldset id="actions">
        <input type="submit" id="submit" value="войти" name="submit">
        <img src="MASH_Security_logo2.svg" width="200px" style="float:left; margin: 0px 0px 0px 100px" 
            alt="foto" >
    </fieldset>
    <a href="" id="back">&#169; Шереметьево безопасность</a>
	<div id="errorLog" class="blink"> <?php echo $hello; // Там самая переменная HELLO_WORLD ?> </div>
	
</form>

</body>
<script>


document.addEventListener("DOMContentLoaded", error);
function error() {
    let err=document.getElementById("errorLog");
    function a() {err.innerHTML="";}
if(err.innerHTML==" Вы ввели неправильный логин/пароль "){ setTimeout(a,5000); }   
}



</script>
</html>
