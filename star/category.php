<?php
if(isset($_COOKIE['id_tpa']))
{
	$link=mysqli_connect("localhost", "root", "", "");
	$query = mysqli_query($link,"SELECT user_name FROM users_act WHERE user_login='".$_COOKIE['id_tpa']."' LIMIT 1");
    $data = mysqli_fetch_assoc($query);
	setcookie("level", "", time() - (86400 * 30));
    setcookie("cat", "", time() - (86400 * 30));
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
    <link href="style_vaiu2.css" rel="stylesheet">
	
<style> body{
-moz-user-select: none;
-khtml-user-select: none;
user-select: none;  
margin: 0px;
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

function cat(x)
{
if (x==3) {setCookie("cat",3);window.location.href = 'category2.php';}


if (x==4) {setCookie("cat",4);window.location.href = 'category2.php';}


if (x==5) {setCookie("cat",5);window.location.href = 'category2.php';}


if (x==6) {setCookie("cat",6);window.location.href = 'category2.php';}


if (x==7) {setCookie("cat",7);window.location.href = 'category2.php';}

}

</SCRIPT>
</head>

<body>
		
  
<div class="main2">
<div class="hello"><br>Уважаемый работник! (<?php  echo $data["user_name"]; ?>) </div>

<div class="hello_description">Перед вами находится тренажёр, созданный специально для подготовки 
    к аттестации по транспортной безопасности. Этот инструмент предназначен для того, 
    чтобы помочь вам лучше усвоить необходимые знания и навыки, которые требуются для успешной аттестации. 
    Вам необходимо выбрать категорию, по которой вы хотите подготовиться. 
    Для этого просто нажмите на необходимую категорию.
<br>
По третьей категории проходит аттестацию руководящий состав, 
что подразумевает проверку его знаний и способностей в области управления безопасностью.
<br> 
Четвёртая категория предназначена для работников группы быстрого реагирования (ГБР), 
которые выполняют критически важные функции в обеспечения безопасности. 
<br>
Пятая категория охватывает инспекторов, непосредственно производящих досмотр, 
работников перронного досмотра, а также сотрудников службы режима и дирекции по досмотру пассажиров и персонала. 
<br>
Шестая категория предназначена для специалистов по профайлингу из отдела 
оперативного реагирования, которые играют ключевую роль в предотвращении инцидентов. 
<br>
Наконец, седьмая категория включает работников дирекции по планированию и диспетчеризации производства, 
отвечающих за координацию и управление процессами. 
<br>
Каждый из этих уровней имеет свои специфические 
требования и задачи, и данный тренажёр поможет вам подготовиться к успешному прохождению аттестации, 
обеспечивая уверенность в своих силах и знаниях.


</div>




<div class="description" onclick = "cat(3)"><b>(3 категория)</b> <br> Работники субъекта транспортной 
    инфраструктуры или подразделения транспортной безопасности, руководящие выполнением работ, непосредственно 
    связанных с обеспечением транспортной безопасности на объектах транспортной 
    инфраструктуры или транспортных средствах </br>
    
</div>

    <div class="description"  onclick = "cat(4)"><b>(4 категория)</b> <br> Работники ПТБ включенные в состав групп быстрого реагирования<br>
    

</div>

    <div class="description"  onclick = "cat(5)"><b>(5 категория) </b><br> Работники подразделения транспортной безопасности, осуществляющие досмотр, 
    дополнительный досмотр и повторный досмотр в целях обеспечения транспортной безопасности<br>
    
</div>

    <div class="description"  onclick = "cat(6)"><b>(6 категория)</b> <br> Работники, осуществляющие наблюдение и (или) 
    собеседование в целях обеспечения транспортной безопасности<br>
   
</div>

    <div class="description"  onclick = "cat(7)"><b>(7 категория)</b> <br> Работники, управляющие техническими 
    средствами обеспечения транспортной безопасности<br>
   
</div>



</div>
 
 

  


<div class="logo">
<img src="MASH_Security_logo2.svg" width="200px" style="float:left; margin: 0px 0px 0px 10px" 
            alt="foto" >    
			<div class="copyrith"> 
			&#169; Шереметьево безопасность
			</div></div>
    
   
 
</body>


</html>
