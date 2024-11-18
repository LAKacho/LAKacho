<?php
mb_internal_encoding("UTF-8");

if (file_exists('Сотрудники.csv')) {
    $lines = file('Сотрудники.csv');
} else {
    exit();
}

$link = mysqli_connect("localhost", "u159215", "ZsX0seor!", "b159215_list");
$linkIntern = mysqli_connect("localhost", "u159215", "ZsX0seor!", "b159215_intern");

function dataRec($datas, $shifts) 
{  
    $weekday = ["воскресенье", "понедельник", "вторник", "среда", "четверг", "пятница", "суббота"];
    if ($shifts == "0") {
        return false;
    }
    $datasEnd = date('d-m-Y', strtotime($datas . '+3 day'));
    $datasEnd = strtotime($datasEnd);

    $dateStart = strtotime("2023-03-02");
    $diffDay = ($datasEnd - $dateStart) / 60 / 60 / 24;
    $diffDay = (int)$diffDay % 4;
    if ($diffDay < 0) {
        $diffDay = 4 + $diffDay;
    }

    $first = date('d-m-Y', $datasEnd);
    $second = date('d-m-Y', strtotime($first . '+1 day'));
    $third = date('d-m-Y', strtotime($first . '+2 day'));

    $shiftPatterns = [
        "1421324314213243",
        "2132431421324314",
        "3243142132431421",
        "4314213243142132"
    ];
    $shiftA = $shiftPatterns[$diffDay];

    $pos = -1;
    $result = [];
    while (($pos = strpos($shiftA, $shifts, $pos + 1)) !== false) {
        $result[] = $pos;
    }

    $meet = ($result[2] % 2 == 0) ? ceil(($result[2] - $result[0]) / 2) : ceil(($result[3] - $result[0]) / 2);
    $dataMeet = date('d-m-Y', strtotime($newDay . '+' . $meet . ' day'));
    $nowDey = $weekday[date("w", strtotime($dataMeet))];
    if ($nowDey == "воскресенье" || $nowDey == "суббота") {
        $dataMeet = date('d-m-Y', strtotime($dataMeet . '+4 day'));
    }
    return date('Y-m-d', strtotime($dataMeet)); 
}

$dd = dataRec("24-05-2023", "2");
print_r($dd);

unset($lines[0]);

$intern = [];

$person = 'SELECT * FROM sd';
$person2 = 'SELECT tb_nomer FROM intern_sd';

if ($res = mysqli_query($link, $person)) { 
    foreach ($res as $row) {
        $i++;
        $per[$i] = $row["login"];
    }
}
$tabInternDiss[0] = 0;
$j = 0;

foreach ($lines as $line_num => $line) { 
    $arr[$line_num] = explode(";", $line);

    if (count($arr[$line_num]) >= 4) {
        $tabnum[$line_num] = "B" . $arr[$line_num][0];
        $tab[$line_num] = (int)$arr[$line_num][0];
        $firstName = trim($arr[$line_num][1]);
        $middleName = trim($arr[$line_num][2]);
        $lastName = trim($arr[$line_num][3]);

        if (!empty($firstName) && !empty($middleName) && !empty($lastName)) {
            $fio[$line_num] = $firstName . " " . $middleName . " " . $lastName;
        }

        $schedule[$line_num] = $arr[$line_num][17];
        $jobTitle[$line_num] = $arr[$line_num][9];
        $shift = preg_replace("/[^0-9]/", '', $arr[$line_num][15]);

        if ($shift == "") {
            $shift = 0;
        }
        if (stripos($jobTitle[$line_num], "стажер") && $shift < 5 && !stripos($schedule[$line_num], "5-")) { 
            $j++;
            if ($arr[$line_num][7] == "") {  
                $intern[$j] = [
                    $tab[$line_num], 
                    $fio[$line_num], 
                    $arr[$line_num][11], 
                    $shift, 
                    $arr[$line_num][6]
                ];
                $tabIntern[$j] = $tab[$line_num];
                $timeIntern[$j] = $arr[$line_num][16];
                $psix[$j] = $arr[$line_num][21];
                $drag[$j] = $arr[$line_num][22];
                $msch[$j] = $arr[$line_num][23];
                $pasp[$j] = $arr[$line_num][24];
                $crim[$j] = $arr[$line_num][25];
                $crimDrag[$j] = $arr[$line_num][26];
                $diplom[$j] = $arr[$line_num][27];
                $trKn[$j] = $arr[$line_num][28];
                $cadet[$j] = (stripos($arr[$line_num][31], "(") !== false) ? 1 : 0;
            } else {
                $tabInternDiss[$j] = $tab[$line_num];
            }                                                                         
        }
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

$diffDismIntern = array_diff($sqlIntern, $tabIntern);
$diffNewIntern = array_diff($tabIntern, $sqlIntern);

echo "<br>ушедшие стажеры "; 
print_r($diffDismIntern);
echo "-------------------------<br>";
echo "<br>новые стажеры "; 
print_r($diffNewIntern);

foreach ($diffNewIntern as $key => $value) {
    $intern_date = date("Y-m-d", strtotime($intern[$key][4]));
    if (stripos($intern[$key][2], "_")) {
        $intern[$key][2] = str_replace("_", "", $intern[$key][2]);
    }

    $internSd = 'INSERT INTO intern_sd VALUES("'.$intern[$key][0].'", "'.$fio[$line_num].'", "'.$intern[$key][2].'", "'.$intern[$key][3].'", "'.$intern_date.'", 0, 0, 0, 0, NULL, "'.$cadet[$key].'", 0, 0, 0, 0, 0, 0, 0, 0, 0)'; 
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
    $clock[0] = $clock[0] == "" ? 0 : (int)$clock[0];
    
    $changeTime = "UPDATE intern_sd SET times=".$clock[0].", pasp_k=".(int)$pasp[$key].
    ", diplom_k=".(int)$diplom[$key].", trkn_k=".$trKn[$key].", msch_k=".$msch[$key].
    ", pnd_k=".$psix[$key].", drag_k=".$drag[$key].", crim_k=".$crim[$key].
    ", crimdrag_k=".$crimDrag[$key].", shift=".$intern[$key][3].", count='".$intern[$key][2]."' WHERE tb_nomer=".$value." AND success=0";

    if ($intern[$key][3] != "0") { 
        $intern_date = date("Y-m-d", strtotime($intern[$key][4]));
        $dd = dataRec($intern_date, $intern[$key][3]);
        $changeTimeMeet = "UPDATE intern_sd SET rec_date='".$dd."' WHERE tb_nomer=".$value." AND success=0 AND rec_date='0000-00-00'"; 
        mysqli_query($linkIntern, $changeTimeMeet);
    }
    mysqli_query($linkIntern, $changeTime);
}

if (!isset($per)) {
    $per[0] = 0;
}
$diffDismissal = array_diff($per, $tabnum);
$diffAccepted = array_diff($tabnum, $per);

foreach ($diffDismissal as $key => $value) {
    $dismissal = 'UPDATE sd SET dismissal = CURRENT_DATE() WHERE login = "'.$value.'" AND dismissal IS NULL';
    $dismissalFio = 'UPDATE names_sd SET dismissal = CURRENT_DATE() WHERE login = "'.$value.'" AND dismissal IS NULL';  

    mysqli_query($link, $dismissal);         
    mysqli_query($link, $dismissalFio);
}

foreach ($diffAccepted as $key => $value) {
    $accepted = 'INSERT INTO sd VALUES("'.$value.'", "12345", "'.$tab[$key].'", NULL)';
    $acceptedFio = 'INSERT INTO names_sd VALUES("'.$value.'", "'.$fio[$line_num].'", NULL)';

    mysqli_query($link, $accepted);        
    mysqli_query($link, $acceptedFio);
}

echo "<br>уволены";                
print_r($diffDismissal); 
echo "<br><br>";
echo "<br>приняты";    
print_r($diffAccepted);
?>