<?php

$link=mysqli_connect("localhost", "root", "", "tpaTB");
$output ='';
 
	
  
  $myVar = json_decode(file_get_contents("php://input"),true); 
  
  $result = $myVar["result"];
  $timess = $myVar["time"];
  $tbNomer = $myVar["tbNomer"];
  $levels =$_COOKIE["level"];
  $TBkat = $_COOKIE["cat"];
 

if ($levels=="teor"||$levels=="prac"){mysqli_query($link,"INSERT INTO tbexam (tb_nomer, levels, TBkat, timess, result) 
  VALUES (".$tbNomer.",'".$levels."',".$TBkat.",".$timess.",".$result.")");} else {


  mysqli_query($link,"INSERT INTO resulttb (tb_nomer, levels, TBkat, timess, result) 
  VALUES (".$tbNomer.",".$levels.",".$TBkat.",".$timess.",".$result.")");}
  
  mysqli_close($link);
  print_r($myVar);

die;

?>

