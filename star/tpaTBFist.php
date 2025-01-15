<?php

$link=mysqli_connect("localhost", "root", "", "tpaTB");
$output ='';
 
	
  
  $myVar = json_decode(file_get_contents("php://input"),true); 
  
  $tbNomer = $myVar["tbNomer"];
  $levels =$_COOKIE["level"];
  $TBkat = $_COOKIE["cat"];


  $query = mysqli_query($link,"SELECT datas FROM firsttime WHERE tb_nomer=".$tbNomer." AND levels='".$levels."' ORDER BY key_id DESC");
  if(isset($query)) {
    $data = mysqli_fetch_assoc($query); 
    if(isset($data["datas"])){
       
    $datas = strtotime($data["datas"]); 
    $datas=date("Y-m-d",$datas); 
    if ($datas == date("Y-m-d")){echo 1;}
    else { mysqli_query($link,"INSERT INTO firsttime (tb_nomer, levels, TBkat) 
        VALUES (".$tbNomer.",'".$levels."',".$TBkat.")");   echo 0; } 
        
    } else {mysqli_query($link,"INSERT INTO firsttime (tb_nomer, levels, TBkat) 
        VALUES (".$tbNomer.",'".$levels."',".$TBkat.")"); echo 5;}
    } 




  
  
  mysqli_close($link);
  

die;

?>

