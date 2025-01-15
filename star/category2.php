<?php
if(isset($_COOKIE['id_tpa']))
{
	$link=mysqli_connect("localhost", "root", "", "xtvr");
	$query = mysqli_query($link,"SELECT user_name FROM users_act WHERE user_login='".$_COOKIE['id_tpa']."' LIMIT 1");
    $data = mysqli_fetch_assoc($query);
    $link=mysqli_connect("localhost", "root", "", "tpatb");
    
    $query = mysqli_query($link,"SELECT AVG(result) AS average_last_three FROM( SELECT result 
    FROM resulttb WHERE tb_nomer=".$_COOKIE['id_tpa']." AND TBkat=".$_COOKIE['cat']." AND levels=1 
    ORDER BY resulttb.key_id DESC LIMIT 3) as last_three");
   
    $dataAVG1 = mysqli_fetch_assoc($query);
    $dataAVG1 = $dataAVG1["average_last_three"]*1;
    $dataAVG1 = round($dataAVG1);
    $dataAVG1 =  $dataAVG1."% ";



    $query = mysqli_query($link,"SELECT AVG(result) AS average_last_three FROM( SELECT result 
    FROM resulttb WHERE tb_nomer=".$_COOKIE['id_tpa']." AND TBkat=".$_COOKIE['cat']." AND levels=2 
    ORDER BY resulttb.key_id DESC LIMIT 3) as last_three");
    
    $dataAVG2 = mysqli_fetch_assoc($query);
    $dataAVG2 = $dataAVG2["average_last_three"];
    $dataAVG2 = round($dataAVG2);
    $dataAVG2 =  $dataAVG2."% ";

    $query = mysqli_query($link,"SELECT AVG(result) AS average_last_three FROM( SELECT result 
    FROM resulttb WHERE tb_nomer=".$_COOKIE['id_tpa']." AND TBkat=".$_COOKIE['cat']." AND levels=3 
    ORDER BY resulttb.key_id DESC LIMIT 3) as last_three");
   
   $dataAVG3 = mysqli_fetch_assoc($query);
    $dataAVG3 = $dataAVG3["average_last_three"];
    $dataAVG3 = round($dataAVG3);
    $dataAVG3 =  $dataAVG3."% ";
    
    $query = mysqli_query($link,"SELECT AVG(result) AS average_last_three FROM( SELECT result 
    FROM resulttb WHERE tb_nomer=".$_COOKIE['id_tpa']." AND TBkat=".$_COOKIE['cat']." AND levels=4 
    ORDER BY resulttb.key_id DESC LIMIT 3) as last_three");
    
    $dataAVG4 = mysqli_fetch_assoc($query);
    $dataAVG4 = $dataAVG4["average_last_three"];
    $dataAVG4 = round($dataAVG4);
    $dataAVG4 =  $dataAVG4."% ";

    $query = mysqli_query($link,"SELECT AVG(result) AS average_last_three FROM( SELECT result 
    FROM resulttb WHERE tb_nomer=".$_COOKIE['id_tpa']." AND TBkat=".$_COOKIE['cat']." AND levels=5 
    ORDER BY resulttb.key_id DESC LIMIT 3) as last_three");
    
    $dataAVG5 = mysqli_fetch_assoc($query);
    $dataAVG5 = $dataAVG5["average_last_three"];
    $dataAVG5 = round($dataAVG5);
    $dataAVG5 =  $dataAVG5."% ";

    $query = mysqli_query($link,"SELECT AVG(result) AS average_last_three FROM( SELECT result 
    FROM resulttb WHERE tb_nomer=".$_COOKIE['id_tpa']." AND TBkat=".$_COOKIE['cat']." AND levels=6 
    ORDER BY resulttb.key_id DESC LIMIT 3) as last_three");
    
    $dataAVG6 = mysqli_fetch_assoc($query);
    $dataAVG6 = $dataAVG6["average_last_three"];
    $dataAVG6 = round($dataAVG6);
    $dataAVG6 =  $dataAVG6."% ";

    $query = mysqli_query($link,"SELECT MAX(result) FROM tbexam WHERE tb_nomer=".$_COOKIE['id_tpa']." 
    AND TBkat=".$_COOKIE['cat']." AND levels='teor' ORDER BY tbexam.key_id DESC");
    
    $dataTeor = mysqli_fetch_array($query);
    //print_r ($dataTeor);
    if(isset($dataTeor[0])) {$dataTeor = $dataTeor[0];} else {$dataTeor=0;}
    $dataTeor = round($dataTeor);
    $dataTeor =  $dataTeor."% ";

    $query = mysqli_query($link,"SELECT MAX(result)  FROM tbexam WHERE tb_nomer=".$_COOKIE['id_tpa']." 
    AND TBkat=".$_COOKIE['cat']." AND levels='prac' 
    ORDER BY tbexam.key_id DESC");
    
    $dataPrac = mysqli_fetch_array($query);
    if(isset($dataPrac[0])) {$dataPrac = $dataPrac[0];} else {$dataPrac=0;}
    $dataPrac = round($dataPrac);
    $dataPrac =  $dataPrac."% ";
   

    
} else {header("Location: index.php"); exit(); }
$description = "";
if(isset($_COOKIE['cat']))
{
$cat = $_COOKIE['cat'];
if ($cat==3) { $description = '<div class="description2" ><b>(3 категория)</b> <br> Работники субъекта транспортной 
инфраструктуры или подразделения транспортной безопасности, руководящие выполнением работ, непосредственно 
связанных с обеспечением транспортной безопасности на объектах транспортной 
инфраструктуры или транспортных средствах </br> <div class="center">
    <button class="custom-btn btn-13" onclick="cat(1)">подготовка теория 1 <br>средняя оценка: '.$dataAVG1.'</button>
    <button class="custom-btn btn-13" onclick="cat(2)">подготовка теория 2 <br>средняя оценка: '.$dataAVG2.'</button>
    <button class="custom-btn btn-13" onclick="cat(3)">подготовка теория 3 <br>средняя оценка: '.$dataAVG3.'</button>
    <button class="custom-btn btn-14" id = "teor" style = "background-image: linear-gradient(-115deg, #ccc 61%, #fca 94%);" 
    onclick="cat(10)">итоговый тест (теория)<br>оценка: '.$dataTeor.'</button>
    <hr>
    <button class="custom-btn btn-13" onclick="cat(4)">подготовка практика 1 <br>средняя оценка: '.$dataAVG4.'</button>
    <button class="custom-btn btn-13" onclick="cat(5)">подготовка практика 2 <br>средняя оценка: '.$dataAVG5.'</button>
    <button class="custom-btn btn-13" onclick="cat(6)">подготовка практика 3 <br>средняя оценка: '.$dataAVG6.'</button>
    <button class="custom-btn btn-14" style = "background-image: linear-gradient(315deg, #fca 61%, #ccc 74%);" 
    onclick="cat(11)">итоговый тест (практика) <br>оценка: '.$dataPrac.'</button>
    </div></div>';}

    if ($cat==4) { $description = '<div class="description2" ><b>(4 категория)</b> <br> Работники ПТБ включенные в состав групп быстрого реагирования<br> <div class="center">
            <button class="custom-btn btn-13" onclick="cat(1)">подготовка теория 1 <br>средняя оценка: '.$dataAVG1.'</button>
            <button class="custom-btn btn-13" onclick="cat(2)">подготовка теория 2 <br>средняя оценка: '.$dataAVG2.'</button>
            <button class="custom-btn btn-13" onclick="cat(3)">подготовка теория 3 <br>средняя оценка: '.$dataAVG3.'</button>
            <button class="custom-btn btn-14" id = "teor" style = "background-image: linear-gradient(-115deg, #ccc 61%, #fca 94%);" 
            onclick="cat(12)">итоговый тест (теория)<br>оценка: '.$dataTeor.'</button>
            <hr>
            <button class="custom-btn btn-13" onclick="cat(4)">подготовка практика 1 <br>средняя оценка: '.$dataAVG4.'</button>
            <button class="custom-btn btn-13" onclick="cat(5)">подготовка практика 2 <br>средняя оценка: '.$dataAVG5.'</button>
            <button class="custom-btn btn-13" onclick="cat(6)">подготовка практика 3 <br>средняя оценка: '.$dataAVG6.'</button>
            <button class="custom-btn btn-14" style = "background-image: linear-gradient(315deg, #fca 61%, #ccc 74%);" 
            onclick="cat(13)">итоговый тест (практика) <br>оценка: '.$dataPrac.'</button>
            </div></div>';}
   if ($cat==5) { $description = '<div class="description2" ><b>(5 категория) </b><br> Работники подразделения транспортной безопасности, осуществляющие досмотр,      дополнительный досмотр и повторный досмотр в целях обеспечения транспортной безопасности<br> <div class="center">
                <button class="custom-btn btn-13" onclick="cat(1)">подготовка теория 1 <br>средняя оценка: '.$dataAVG1.'</button>
                <button class="custom-btn btn-13" onclick="cat(2)">подготовка теория 2 <br>средняя оценка: '.$dataAVG2.'</button>
                <button class="custom-btn btn-13" onclick="cat(3)">подготовка теория 3 <br>средняя оценка: '.$dataAVG3.'</button>
                <button class="custom-btn btn-14" id = "teor" style = "background-image: linear-gradient(-115deg, #ccc 61%, #fca 94%);" 
                onclick="cat(14)">итоговый тест (теория)<br>оценка: '.$dataTeor.'</button>
                <hr>
                <button class="custom-btn btn-13" onclick="cat(4)">подготовка практика 1 <br>средняя оценка: '.$dataAVG4.'</button>
                <button class="custom-btn btn-13" onclick="cat(5)">подготовка практика 2 <br>средняя оценка: '.$dataAVG5.'</button>
                <button class="custom-btn btn-13" onclick="cat(6)">подготовка практика 3 <br>средняя оценка: '.$dataAVG6.'</button>
                <button class="custom-btn btn-14" style = "background-image: linear-gradient(315deg, #fca 61%, #ccc 74%);" 
                onclick="cat(15)">итоговый тест (практика) <br>оценка: '.$dataPrac.'</button>
                </div></div>';}
   if ($cat==6) { $description = '<div class="description2" ><b>(6 категория)</b> <br> Работники, осуществляющие наблюдение и (или) 
            собеседование в целях обеспечения транспортной безопасности<br><div class="center">
                    <button class="custom-btn btn-13" onclick="cat(1)">подготовка теория 1 <br>средняя оценка: '.$dataAVG1.'</button>
                    <button class="custom-btn btn-13" onclick="cat(2)">подготовка теория 2 <br>средняя оценка: '.$dataAVG2.'</button>
                    <button class="custom-btn btn-13" onclick="cat(3)">подготовка теория 3 <br>средняя оценка: '.$dataAVG3.'</button>
                    <button class="custom-btn btn-14" id = "teor" style = "background-image: linear-gradient(-115deg, #ccc 61%, #fca 94%);" 
                    onclick="cat(16)">итоговый тест (теория)<br>оценка: '.$dataTeor.'</button>
                    <hr>
                    <button class="custom-btn btn-13" onclick="cat(4)">подготовка практика 1 <br>средняя оценка: '.$dataAVG4.'</button>
                    <button class="custom-btn btn-13" onclick="cat(5)">подготовка практика 2 <br>средняя оценка: '.$dataAVG5.'</button>
                    <button class="custom-btn btn-13" onclick="cat(6)">подготовка практика 3 <br>средняя оценка: '.$dataAVG6.'</button>
                    <button class="custom-btn btn-14" style = "background-image: linear-gradient(315deg, #fca 61%, #ccc 74%);" 
                    onclick="cat(17)">итоговый тест (практика) <br>оценка: '.$dataPrac.'</button>
                    </div></div>';}
  if ($cat==7) { $description = '<div class="description2" ><b>(7 категория)</b> <br> Работники, управляющие техническими 
                средствами обеспечения транспортной безопасности<br><div class="center">
                                <button class="custom-btn btn-13" onclick="cat(1)">подготовка теория 1 <br>средняя оценка: '.$dataAVG1.'</button>
                                <button class="custom-btn btn-13" onclick="cat(2)">подготовка теория 2 <br>средняя оценка: '.$dataAVG2.'</button>
                                <button class="custom-btn btn-13" onclick="cat(3)">подготовка теория 3 <br>средняя оценка: '.$dataAVG3.'</button>
                                <button class="custom-btn btn-14" id = "teor" style = "background-image: linear-gradient(-115deg, #ccc 61%, #fca 94%);" 
                                onclick="cat(18)">итоговый тест (теория)<br>оценка: '.$dataTeor.'</button>
                                <hr>
                                <button class="custom-btn btn-13" onclick="cat(4)">подготовка практика 1 <br>средняя оценка: '.$dataAVG4.'</button>
                                <button class="custom-btn btn-13" onclick="cat(5)">подготовка практика 2 <br>средняя оценка: '.$dataAVG5.'</button>
                                <button class="custom-btn btn-13" onclick="cat(6)">подготовка практика 3 <br>средняя оценка: '.$dataAVG6.'</button>
                                <button class="custom-btn btn-14" style = "background-image: linear-gradient(315deg, #fca 61%, #ccc 74%);" 
                                onclick="cat(19)">итоговый тест (практика) <br>оценка: '.$dataPrac.'</button>
                                </div></div>';}
            
        

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
    <link href="style_vaiu2.css" rel="stylesheet">
	
<style> body{
-moz-user-select: none;
-khtml-user-select: none;
user-select: none;  
margin: 0px;
} 

p{text-indent: 3ch;margin:3px;}
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

function cat(x)
{
if (x==1) {setCookie("level",1);window.location.href = 'welc.php';}
if (x==2) {setCookie("level",2);window.location.href = 'welc.php';}
if (x==3) {setCookie("level",3);window.location.href = 'welc.php';}
if (x==10||x==12||x==14||x==16||x==18) {setCookie("level","teor");window.location.href = 'welc.php';}

if (x==4) {setCookie("level",4);window.location.href = 'welc.php';}
if (x==5) {setCookie("level",5);window.location.href = 'welc.php';}
if (x==6) {setCookie("level",6);window.location.href = 'welc.php';}
if (x==11||x==13||x==15||x==17||x==19) {setCookie("level","prac");window.location.href = 'welc.php';}

}

</SCRIPT>
</head>

<body>
		
  
<div class="main2">
<div class="hello"></div>

<div class="hello_description"><b><u><?php  echo $data["user_name"]; ?> </u></b> - аттестация по транспортной безопасности является важной 
    процедурой, целью которой является оценка знаний и навыков, необходимых для обеспечения 
    безопасности на всех уровнях транспортного процесса. Процесс аттестации включает в себя как 
    теоретическую проверку, так и практическую часть, позволяющую проверить не только знание теории, 
    но и способность применять эти знания на практике. <br>

<p>Для того чтобы облегчить вашу подготовку, вся программа разбита на четыре этапа. <b>На первом этапе</b>, если вы допустите неверный ответ, <b>система предоставит вам верный 
вариант ответа</b>, позволяя понять, где была ошибка и что следует учитывать в будущем.
 Это важно для формирования правильного понимания материалов. 

 <p><b>На втором этапе возможность увидеть правильный ответ уже отключается.</b> 
Это помогает вам самостоятельно проанализировать свои знания и учиться, 
основываясь на собственных выводах. Наконец, <b>на третьем этапе добавляется 
дополнительный элемент сложности — вы будете ограничены по времени</b> при ответе 
на каждый вопрос. Это помогает подготовиться к настоящим условиям, когда необходимо
 быстро реагировать на вызовы.<p>
 <b>На четвертом этапе вам предлагается пройти подготовку к тестированию, максимально приближенную к аттестации.</b>
    Вам дается 60 минут на теоретическую часть и 20 мин на практическую. <b style="color: red">Этот результат будет рассматриваться 
    в качестве допуска на аттестацию.</b> 
 
 <p>В теоретической части аттестации вам будут представлены 
несколько вариантов ответов на вопросы, среди которых нужно выбрать только один 
правильный. Это тестирование проверяет вашу теоретическую подготовленность. В практической 
части задачи будут несколько сложнее: вам необходимо выбрать два правильных ответа.
Это проверяет вашу способность анализировать ситуацию и принимать решения, опираясь на знания.

<p>Кроме того, для вашего удобства система будет отображать среднюю оценку по каждому 
этапу подготовки на основе ваших последних трех попыток. Это позволит вам отслеживать 
прогресс и оценивать, насколько эффективно вы готовитесь к аттестации. Мы желаем вам удачи 
в подготовке и уверенности в ваших знаниях! 
<br>
<a href="category.php">Выбрать другую категорию.</a>

</div>
<?php  echo $description; ?>

    
<!--


<div class="description" ><b>(3 категория)</b> <br> Работники субъекта транспортной 
    инфраструктуры или подразделения транспортной безопасности, руководящие выполнением работ, непосредственно 
    связанных с обеспечением транспортной безопасности на объектах транспортной 
    инфраструктуры или транспортных средствах </br>
    
</div>

    <div class="description" ><b>(4 категория)</b> <br> Работники ПТБ включенные в состав групп быстрого реагирования<br>
    <button class="custom-btn btn-13" onclick="cat(41)">подготовка 1</button>
    <button class="custom-btn btn-13" onclick="cat(42)">подготовка 2</button>
    <button class="custom-btn btn-13" onclick="cat(43)">подготовка 3</button>

</div>

    <div class="description" ><b>(5 категория) </b><br> Работники подразделения транспортной безопасности, осуществляющие досмотр, 
    дополнительный досмотр и повторный досмотр в целях обеспечения транспортной безопасности<br>
    <button class="custom-btn btn-13" onclick="cat(51)">подготовка 1</button>
    <button class="custom-btn btn-13" onclick="cat(52)">подготовка 2</button>
    <button class="custom-btn btn-13" onclick="cat(53)">подготовка 3</button>
</div>

    <div class="description" ><b>(6 категория)</b> <br> Работники, осуществляющие наблюдение и (или) 
    собеседование в целях обеспечения транспортной безопасности<br>
    <button class="custom-btn btn-13" onclick="cat(61)">подготовка 1</button>
    <button class="custom-btn btn-13" onclick="cat(62)">подготовка 2</button>
    <button class="custom-btn btn-13" onclick="cat(63)">подготовка 3</button>
</div>

    <div class="description" ><b>(7 категория)</b> <br> Работники, управляющие техническими 
    средствами обеспечения транспортной безопасности<br>
    <button class="custom-btn btn-13" onclick="cat(71)">подготовка 1</button>
    <button class="custom-btn btn-13" onclick="cat(72)">подготовка 2</button>
    <button class="custom-btn btn-13" onclick="cat(73)">подготовка 3</button>
</div>



</div>
 
 
-->
  


<div class="logo">
<img src="MASH_Security_logo2.svg" width="200px" style="float:left; margin: 0px 0px 0px 10px" 
            alt="foto" >    
			<div class="copyrith"> 
			&#169; Шереметьево безопасность
			</div></div>
    
    
 
</body>
<script LANGUAGE="JavaScript">
function is_mobile() {return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));}
if(is_mobile()){document.getElementById("teor").disabled=true;}
</script>

</html>
