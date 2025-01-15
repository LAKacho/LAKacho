<?php
if(isset($_COOKIE['id_tpa']))
{
	$link=mysqli_connect("localhost", "root", "", "xtvr");
	$query = mysqli_query($link,"SELECT user_name FROM users_act WHERE user_login='".$_COOKIE['id_tpa']."' LIMIT 1");
    $data = mysqli_fetch_assoc($query);
	

} else {header("Location: index.php"); exit(); }

if(isset($_COOKIE['cat'])&&isset($_COOKIE['level']))
{	$exam = true;
	if ($_COOKIE['cat']=="3"&&$_COOKIE['level']=="teor")
			{$exam = false; echo "<script src='test_3k.js'></script>"; echo "<script src='vueT.js'></script>";}	
	if ($_COOKIE['cat']=="3"&&$_COOKIE['level']=="prac")
			{$exam = false; echo "<script src='test_3p.js'></script>"; echo "<script src='vueP.js'></script>";}	
  if ($_COOKIE['cat']=="4"&&$_COOKIE['level']=="teor")
			{$exam = false; echo "<script src='test_4k.js'></script>"; echo "<script src='vueT.js'></script>";}	
	if ($_COOKIE['cat']=="4"&&$_COOKIE['level']=="prac")
			{$exam = false; echo "<script src='test_4p.js'></script>"; echo "<script src='vueP.js'></script>";}	
  if ($_COOKIE['cat']=="5"&&$_COOKIE['level']=="teor")
			{$exam = false; echo "<script src='test_5k.js'></script>"; echo "<script src='vueT.js'></script>";}	
	if ($_COOKIE['cat']=="5"&&$_COOKIE['level']=="prac")
			{$exam = false; echo "<script src='test_5p.js'></script>"; echo "<script src='vueP.js'></script>";}	
  if ($_COOKIE['cat']=="6"&&$_COOKIE['level']=="teor")
			{$exam = false; echo "<script src='test_6k.js'></script>"; echo "<script src='vueT.js'></script>";}	
	if ($_COOKIE['cat']=="6"&&$_COOKIE['level']=="prac")
			{$exam = false; echo "<script src='test_6p.js'></script>"; echo "<script src='vueP.js'></script>";}
  if ($_COOKIE['cat']=="7"&&$_COOKIE['level']=="teor")
			{$exam = false; echo "<script src='test_7k.js'></script>"; echo "<script src='vueT.js'></script>";}	
	if ($_COOKIE['cat']=="7"&&$_COOKIE['level']=="prac")
			{$exam = false; echo "<script src='test_7p.js'></script>"; echo "<script src='vueP.js'></script>";}		
	
	//-------------------------------------------------------------------------------
	
	
	if ($_COOKIE['cat']=="3"&&$_COOKIE['level']<4&&$exam)
	{echo "<script src='test_3k.js'></script>"; echo "<script src='vue.js'></script>";}
	
	if ($_COOKIE['cat']=="4"&&$_COOKIE['level']<4&&$exam)
	{echo "<script src='test_4k.js'></script>";echo "<script src='vue.js'></script>";}
	
	if ($_COOKIE['cat']=="5"&&$_COOKIE['level']<4&&$exam)
	{echo "<script src='test_5k.js'></script>";echo "<script src='vue.js'></script>";}
	
	if ($_COOKIE['cat']=="6"&&$_COOKIE['level']<4&&$exam)
	{echo "<script src='test_6k.js'></script>";echo "<script src='vue.js'></script>";}
	
	if ($_COOKIE['cat']=="7"&&$_COOKIE['level']<4&&$exam)
	{echo "<script src='test_7k.js'></script>"; echo "<script src='vue.js'></script>";}
	
//------------------------------------------------	
	
	
	if ($_COOKIE['cat']=="3"&&$_COOKIE['level']>3&&$exam)
	{echo "<script src='test_3p.js'></script>"; echo "<script src='vue2.js'></script>";}
	
	if ($_COOKIE['cat']=="4"&&$_COOKIE['level']>3&&$exam)
	{echo "<script src='test_4p.js'></script>";echo "<script src='vue2.js'></script>";}
	
	if ($_COOKIE['cat']=="5"&&$_COOKIE['level']>3&&$exam)
	{echo "<script src='test_5p.js'></script>";echo "<script src='vue2.js'></script>";}
	
	if ($_COOKIE['cat']=="6"&&$_COOKIE['level']>3&&$exam)
	{echo "<script src='test_6p.js'></script>";echo "<script src='vue2.js'></script>";}
	
	if ($_COOKIE['cat']=="7"&&$_COOKIE['level']>3&&$exam)
	{echo "<script src='test_7p.js'></script>";echo "<script src='vue2.js'></script>";}
//----------------------------------------------------------------
	
			
} else {header("Location: index.php"); exit(); }

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <!--[if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script><![endif]-->
    <title></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link href="style_vaiu.css" rel="stylesheet">
	
<style> body{
-moz-user-select: none;
-khtml-user-select: none;
user-select: none;  
} 

 .popup {
          width: 460px;
          background: #fff;
          position: fixed;
          top: 50%;
          left: 50%;
          z-index: 1001;
          display: none;
          box-shadow: 2px 2px 10px #ccc;
           
        }
        
        .overlay {
          background: #000;
          opacity: 0.5;
          position: fixed;
          top: 0;
          left: 0;
          height: 100%;
          width: 100%;
          z-index: 999;
          display: none;
        }
    
        .ccontent {
          position: relative;
          padding: 50px 50px 20px 50px;
        }
    
        .close {
          position: absolute;
          top: 20px;
          right: 20px;
          width: 70px;
          height: 20px;
          cursor: pointer;
          line-height: 20px;
          color: red;
          font-size: 1.0em;
          background-color: yellow;
        }
        
        .message {
          position: relative;
          bottom: 6px;
          
          width: auto;
          height: auto;
          text-align: center;
          
          color: green;
          font-size: 0.8em;
        }
        #video { 
        z-index: 1005;
        } 
  


</style>

</head>

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

/*function addScript(src){
	var script = document.createElement('script');
	script.src = src;
	script.async = false; // чтобы гарантировать порядок 
	document.head.appendChild(script);
  }
  addScript("test_3k.js");
  addScript("vue.js");	*/

</SCRIPT>

</head>

<body>
<div class="naming"><img id="vaiu" src="vaiu2.jpg" width="54px" style=" 
            alt=foto" >VaiuТест</div>


			
<div class="sphere">  </div>
			<div class="cube"></div>
			<div class="cube2"><img src="images/1.jpg" id="imageFon"
  alt="lorem"></div>
<div class="parent"><div class="main" id="main">
<div class="clock" id="clock"><div id="timers"></div></div>
<div class="question" id="question">Уважаемый работник! 

</div>


<div class="answer" id="answer" >ОТВЕТЫ:<br> 



</div>
<div class="container" id="container2" >



</div>
</div>
</div>

 
 

  

<div class="name">подготовка для: <br><?php  echo $data["user_name"]; ?> <br><span id="dataTime"></span>
<div class="logo">
<img src="MASH_Security_logo2.svg" width="200px" style="float:left; margin: 0px 0px 0px 10px" 
            alt="foto" >    
			<div class="copyrith"> 
			&#169; Шереметьево безопасность
			</div></div>
 

	
	
	
	<div class="popup">
    <div class="ccontent">
      <div class="close" id="close">Закрыть</div>
      
	  <video id="video" width="360" height="240" autoplay></video>
	  <div class="message"><br>Убедитесь что Вас видно!!! (в противном случае результаты тестирования аннулируются)
  </div>
  </div>
  </div>
<div class="overlay"></div>
	</div> 
 
</body>

</html>
