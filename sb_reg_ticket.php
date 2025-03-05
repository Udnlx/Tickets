<?php

error_reporting(E_ERROR | E_PARSE);

$sb_log = '';
$seat_busy = 'off';

$sb_idbus = $_POST['sb_idbus'];
$sb_seat = $_POST['sb_seat'];

// $sb_log .= $sb_idbus;
// $sb_log .= $sb_seat;

if ($sb_idbus && $sb_seat) {

//Подключаемся
try{
    $param = array(
    'login' => 'atp5027241683-web',
    'password' => 'atp5027241683022020web0924',
    'trace' => true,
    'cache_wsdl' => 0,
    'encoding' => 'utf-8',
    'location' => 'http://cluster.avtovokzal.ru/gds114/soap/json',
    );
    $client = new SoapClient('http://cluster.avtovokzal.ru/gds114/soap/json?wsdl', $param);
    $sb_log .= '<p style="color:green;margin:0;">Подключение к 1C прошло успешно</p>';
}
catch (SoapFault $soapFault){
    $sb_log .=  '<p style="color:red;margin:0;">Не подключились к 1C</p>';
    $info_json = json_encode($soapFault);
    $sb_log .=  '<p style="color:red;">' . $info_json . '</p>';
}
//Подключаемся



//Получаем свободные места
try{
    $dataSeat = $client->getRaceSeats(["raceCode"=>'' . $sb_idbus . '']);
}
catch (SoapFault $soapFault){
    $sb_log .=  '<p style="color:red;margin:0;">Не удалось вызвать функцию</p>';
    $info_json = json_encode($soapFault);
    $sb_log .=  '<p style="color:red;">' . $info_json . '</p>';
}

$array_seat = json_decode($dataSeat->return, JSON_UNESCAPED_UNICODE);
// echo '<pre>'; 
// var_dump($array_seat);
// echo '</pre>';
//Получаем свободные места



//Проверяем, свободно ли место которое сейчас будем регистрировать и получаем его ID
$sb_free_seats = [];
foreach ($array_seat as $seat) {
    $str = mb_substr($seat['name'], -2, 2);
    $str = sprintf("%02d", $str);
    if ($str == $sb_seat) {
        $sb_seat_id = $seat['code'];
        //echo $sb_seat_id;
    }
    $sb_free_seats[] = $str;
}
// foreach ($sb_free_seats as $sb_free_seat) {
//     $sb_log .=  $sb_free_seat . ', ';
// }
if (in_array("00", $sb_free_seats)) {
    $sb_log .=  '<p style="color:red;margin:0;">Регистрация в 1С не прошла, получен нулевой массив мест</p>';
}
if (in_array($sb_seat, $sb_free_seats)) {
    $sb_log .=  '<p style="color:red;margin:0;">Регистрация в 1С не прошла, место уже занято</p>';
    $seat_busy = 'on';
} else {
    $seat_busy = 'off';
}
//Проверяем, свободно ли место которое сейчас будем регистрировать и получаем его ID



//Регистрируем билет
if ($seat_busy == 'off') {
    $sb_log .= '<p style="color:green;margin:0;">Билет успешно зарегистрирован в системе 1С<br>(функционал находится в разработке, регистрация пока не доступна)</p>';
}
//Регистрируем билет



//Выводим результат
echo '<div style="text-align:center;">'. $sb_log . '</div>';
//Выводим результат

} else {

echo '<div style="text-align:center;"><p style="color:red;">Регистрация в 1С не прошла, ошибка в данных</p></div>';

}

