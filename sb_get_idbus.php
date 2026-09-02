<?php

$sbidStationStart = $_POST['sbidStationStart'];
$sbidStationFinish = $_POST['sbidStationFinish'];
$sb_dispatch_date = $_POST['sbDispatchDate'];
$sb_dispatch_time = $_POST['sbDispatchTime'];

// Собираем полную дату+время для поиска (формат как в API: "2026-09-30 21:00:00")
$sb_dispatch_datetime = $sb_dispatch_date . ' ' . $sb_dispatch_time;

$found = false;

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
    // echo '<h2>Подключение прошло успешно</h2>';
}
catch (SoapFault $soapFault){
    // echo '<h2>не подключились</h2>';
    // echo '<pre>'; 
    // var_dump($soapFault);
    // echo '</pre>';
}

try{
    $dataList = $client->getRaces(["dispatchPlaceId"=>$sbidStationStart,"arrivalPlaceId"=>$sbidStationFinish,"date"=>$sb_dispatch_date]);
    // echo '<h2>Функция на сервер отправлена</h2>';
}
catch (SoapFault $soapFault){
    // echo '<h2>не удалось вызвать функцию</h2>';
    // echo '<pre>'; 
    // var_dump($soapFault);
    // echo '</pre>';
}

$dataListjson = json_decode($dataList->return, JSON_UNESCAPED_UNICODE);

$uid_bus = '';
$found_race = null;

// Ищем рейс с нужным временем отправления
foreach ($dataListjson as $race) {
    if (isset($race['dispatchDate']) && $race['dispatchDate'] === $sb_dispatch_datetime) {
        $found_race = $race;
        break;
    }
}

if ($found_race && isset($found_race['uid'])) {
    $uid_bus = $found_race['uid'];
    echo $uid_bus;
} else {
    echo 'Автобус не найден';
}