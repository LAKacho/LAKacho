<?php
mb_internal_encoding("UTF-8");

if (file_exists('Сотрудники.csv')) {
    $lines = file('Сотрудники.csv');
} else {
    exit();
}
$link = mysqli_connect("localhost", "u159215", "ZsX0seor!", "b159215_list");
$linkIntern = mysqli_connect("localhost", "u159215", "ZsX0seor!", "b159215_intern");
//test

function dataRec($datas, $shifts) 
{  
    $weekday = ["воскресенье", "понедельник", "вторник", "среда", "четверг", "пятница", "суббота"];
    //$shifts=(int)$shifts;
    if ($shifts == "0") {
        return false;
    }
    $datasEnd = date('d-m-Y', strtotime($datas . '+3 day')); 
    //print_r ("<br>**конец обучения****".$datasEnd." смена - ".$shifts);
    $datasEnd = strtotime($datasEnd);   

    $dateStart = strtotime("2023-03-02");
    $diffDay = ($datasEnd - $dateStart) / 60 / 60 / 24;
    //echo "<br> Разницa - ".$diffDay;
    $diffDay = (int)$diffDay % 4;             
    if ($diffDay < 0) {
        $diffDay = 4 + $diffDay;
    }
    //echo "<br> сдвиг ".$diffDay;            

    $first = date('d-m-Y', $datasEnd);
    $second = date('d-m-Y', strtotime($first . '+1 day'));
    $third = date('d-m-Y', strtotime($first . '+2 day'));          

    if ($diffDay == 0) {$shiftA = "1421324314213243";}
    if ($diffDay == 1) {$shiftA = "2132431421324314";}
    if ($diffDay == 2) {$shiftA = "3243142132431421";}
    if ($diffDay == 3) {$shiftA = "4314213243142132";}             

    //echo "<br>1111-".stripos($shiftA,$shifts);

    $pos = -1;
    $result = [];
    while (($pos = strpos($shiftA, $shifts, $pos + 1)) !== false) {
        $result[] = $pos;
    }

    /*if(stripos($shiftA,$shifts)===0) { echo "<br>первый рабочий день ".$first." ".$weekday[date("w",strtotime($first))]." смены - ".$shiftA;  $newDay=$first;}            
     if(stripos($shiftA,$shifts)===1) { echo "<br>первая рабочая ночь ".$first." ".$weekday[date("w",strtotime($first))]." смены - ".$shiftA; $newDay=$first;}             
     if(stripos($shiftA,$shifts)===2) { echo "<br>первый рабочий день ".$second." ".$weekday[date("w",strtotime($second))]." смены - ".$shiftA; $newDay=$second;} 
      if(stripos($shiftA,$shifts)===3) { echo "<br>первая рабочая ночь ".$second." ".$weekday[date("w",strtotime($second))]." смены - ".$shiftA; $newDay=$second; }             
      if(stripos($shiftA,$shifts)===4) { echo "<br>первый рабочий день ".$third." ".$weekday[date("w",strtotime($third))]." смены - ".$shiftA; $newDay=$third; }*/            

    if (($result[2] % 2) == 0) {
        $meet = ceil(($result[2] - $result[0]) / 2);
    } else {
        $meet = ceil(($result[3] - $result[0]) / 2);
    }
    //echo "<br>result2 - ".$result[2]."result3 - ".$result[3]."<br>" ;  
    //print_r(" +day ".$meet);  echo "<br>";
    $dataMeet = date('d-m-Y', strtotime($newDay . '+' . $meet . ' day')); 
    //print_r($dataMeet." "); print_r($weekday[date("w",strtotime($dataMeet))]); echo "<br>";
    $nowDey = $weekday[date("w", strtotime($dataMeet))];
    if ($nowDey == "воскресенье" || $nowDey == "суббота") {
        $dataMeet = date('d-m-Y', strtotime($dataMeet . '+4 day'));
        $nowDey = $weekday[date("w", strtotime($dataMeet))];
        /*print_r($nowDey."новая встреча: ".$dataMeet." ".$nowDey."<br>" ); */
    }
    return date('Y-m-d', strtotime($dataMeet)); 
}

$dd = dataRec("24-05-2023", "2");
print_r($dd);

//УБЕРАЕМ НАЗВАНИЯ СТОЛБЦОВ
unset($lines[0]); 

$intern = [];

//ЧИТАЕМ ТАБЛИЦЫ sd И names_sd
$person = 'SELECT * FROM sd';
$person2 = 'SELECT tb_nomer FROM intern_sd';

//из таблицы sd табельные 
if ($res = mysqli_query($link, $person)) { 
    foreach ($res as $row) {
        $i++;
        $per[$i] = $row["login"];
    }
}
$tabInternDiss[0] = 0;
$j = 0;
//читаем из файла табельные номера и фио
foreach ($lines as $line_num => $line) { 
    $arr[$line_num] = explode(";", $line);
    if ($arr[$line_num][11] != "") { 
        $tabnum[$line_num] = "B" . $arr[$line_num][0];
        $tab[$line_num] = (int)$arr[$line_num][0];
        $fio[$line_num] = $arr[$line_num][1] . " " . $arr[$line_num][2] . " " . $arr[$line_num][3];
        $fio2[$line_num] = $arr[$line_num][2] . " " . $arr[$line_num][3];

        $schedule[$line_num] = $arr[$line_num][17];
        $jobTitle[$line_num] =  $arr[$line_num][9]; //должность
        $shift = preg_replace("/[^0-9]/", '', $arr[$line_num][15]);

        if ($shift == "") {
            $shift = 0;
        }
        if (stripos($jobTitle[$line_num], "стажер") && $shift < 5 && !stripos($schedule[$line_num], "5-")) { 
            $j++;
            if ($arr[$line_num][7] == "") {  
                echo $j . "стажер " . $tab[$line_num] . " " . $fio[$line_num] . "smena" . $shift . "<br>"; 
                $intern[$j][0] = $tab[$line_num]; 
                $intern[$j][1] = $fio[$line_num]; 
                $intern[$j][2] = $arr[$line_num][11]; 
                $intern[$j][3] = $shift; 
                $intern[$j][4] = $arr[$line_num][6];    
                $tabIntern[$j] = $tab[$line_num];
                $timeIntern[$j] = $arr[$line_num][16]; 
                //справки
                $psix[$j] = $arr[$line_num][21];
                $drag[$j] = $arr[$line_num][22];
                $msch[$j] = $arr[$line_num][23];
                $pasp[$j] = $arr[$line_num][24];
                $crim[$j] = $arr[$line_num][25];
                $crimDrag[$j] = $arr[$line_num][26];
                $diplom[$j] = $arr[$line_num][27];
                $trKn[$j] = $arr[$line_num][28];
                //echo $arr[$line_num][31]."$arr[$line_num][31]<br>";
                if (stripos($arr[$line_num][31], "(") !== false) {
                    $cadet[$j] = 1;
                } else {
                    $cadet[$j] = 0;
                } //стажеры и прочая полиция
            } else {
                $tabInternDiss[$j] = $tab[$line_num];
            }                                                                         
        }   

        $fio2[$line_num] = mb_convert_encoding($fio2[$line_num], 'windows-1251', 'utf-8');
        $fio3[$line_num] = mb_convert_encoding($fio[$line_num], 'windows-1251', 'utf-8'); 
    } 
}

$i = 0;    
if ($resIntern = mysqli_query($linkIntern, $person2)) { 
    foreach ($resIntern as $row) {
        $i++;
        $sqlIntern[$i] = (int)$row["tb_nomer"];
    }
}

if (!$sqlIntern) {
    $sqlIntern[0] = 0;
}
if (!$tabIntern) {
    $tabIntern[0] = 0;
}

$diffDismIntern = array_diff($sqlIntern, $tabIntern); //ушедшие стажеры
$diffNewIntern = array_diff($tabIntern, $sqlIntern); //новые стажеры

echo "<br>ушедшие стажеры "; 
print_r($diffDismIntern);
echo "-------------------------<br>";
echo "<br>новые стажеры "; 
print_r($diffNewIntern);

foreach ($diffNewIntern as $key => $value) {
    //$intern_name = mb_convert_encoding($intern[$key][1], 'windows-1251', 'utf-8');
    $intern_date = date("Y-m-d", strtotime($intern[$key][4])); 
    // новая штатка
    echo $intern[$key][2] . " старое значение<br>";
    if (stripos($intern[$key][2], "_")) {
        $intern[$key][2] = str_replace("_", "", $intern[$key][2]);
    }
    echo $intern[$key][2] . " новое значение<br>";

    $internSd = 'INSERT intern_sd VALUES("'.$intern[$key][0].'","'.$intern_name.'","'.$intern[$key][2].'","'.$intern[$key][3].'","'.$intern_date.'",0,0,0,0,NULL,"'.$cadet[$key].'",0,0,0,0,0,0,0,0,0)'; 
    mysqli_query($linkIntern, $internSd);
}

foreach ($diffDismIntern as $key => $value) {
    if (in_array($value, $tab) && !in_array($value, $tabInternDiss)) {
        $changeInter = "UPDATE intern_sd SET success=3, data_change='".date("Y-m-d")."' WHERE data_change='0000-00-00' AND tb_nomer=".$value;
    } else {
        $changeInter = "UPDATE intern_sd SET success=1, data_change='".date("Y-m-d")."' WHERE data_change='0000-00-00' AND tb_nomer=".$value;
    }
    mysqli_query($linkIntern, $changeInter); 
}

foreach ($tabIntern as $key => $value) {
    $clock = explode(',', $timeIntern[$key]);
    if ($clock[0] == "") {
        $clock[0] = 0;
    }
    $clock[0] = (int)$clock[0];
    echo "подр-" . $intern[$key][2];
    // время работы и справки и подразделение
    $changeTime = " UPDATE intern_sd SET times = ".$clock[0].", pasp_k = ".(int)$pasp[$key].
    ", diplom_k =".(int)$diplom[$key].", trkn_k =".$trKn[$key].", msch_k =".$msch[$key].
    ", pnd_k =".$psix[$key].", drag_k =".$drag[$key].", crim_k =".$crim[$key].
    ", crimdrag_k =".$crimDrag[$key].", shift=".$intern[$key][3].", count='".$intern[$key][2]."' WHERE tb_nomer=".$value. " AND success=0";

    //, cadet=".$cadet[$key]." внести в запрос $changeTime если появятся кадеты
    
    //echo "<br> UPDATE intern_sd SET times = ".$clock[0].", pasp-k =".(int)$pasp[$key].", shift=".$intern[$key][3]. " WHERE tb_nomer=".$value. " AND success=0";

    if ($intern[$key][3] != "0") { 
        $intern_date = date("Y-m-d", strtotime($intern[$key][4]));
        $dd = dataRec($intern_date, $intern[$key][3]); //AND rec_date='0000-00-00'
        $changeTimeMeet = " UPDATE intern_sd SET rec_date='".$dd."' WHERE tb_nomer=".$value."  AND success=0 AND rec_date='0000-00-00'"; 
        mysqli_query($linkIntern, $changeTimeMeet);
    }

    mysqli_query($linkIntern, $changeTime); 
    //mysqli_query($linkIntern, $changeRefer); 
}

if (!isset($per)) {
    $per[0] = 0;
}

$diffDismissal = array_diff($per, $tabnum); //уволенные
$diffAccepted = array_diff($tabnum, $per); //принятые

//ставим дату увольнения    
foreach ($diffDismissal as $key => $value) {
    $dismissal = 'UPDATE sd SET dismissal = CURRENT_DATE() WHERE login = "'.$value.'" AND dismissal IS NULL';
    $dismissalFio = 'UPDATE names_sd SET dismissal = CURRENT_DATE() WHERE login = "'.$value.'" AND dismissal IS NULL';  

    mysqli_query($link, $dismissal);         
    mysqli_query($link, $dismissalFio);
}

//записываем в базу новеньких    
foreach ($diffAccepted as $key => $value) {
    $accepted = 'INSERT sd VALUES("'.$value.'","12345","'.$tab[$key].'",NULL)';
    $acceptedFio = 'INSERT names_sd VALUES("'.$value.'","'.$fio2[$key].'","'.$fio3[$key].'","'.$tab[$key].'",NULL)';
    
    mysqli_query($link, $accepted);        
    mysqli_query($link, $acceptedFio);
}
echo "<br>уволены";                
print_r($diffDismissal); 
echo "<br><br>";
echo "<br>приняты";    
print_r($diffAccepted);
?>