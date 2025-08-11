<?php

$sbidStationStart = $_POST['sbidStationStart'];
$sbidStationFinish = $_POST['sbidStationFinish'];
$sb_dispatch_date = $_POST['sbDispatchDate'];

// echo $sbidStationStart . ' - ' . $sbidStationFinish;

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
// echo '<pre>'; 
// var_dump($dataListjson);
// echo '</pre>';

$uid_bus = '';
$array = $dataListjson[0];
if ($array['uid']) {
	$uid_bus = $array['uid'];
	echo $uid_bus;
} else {
	echo 'Автобус не найден';
}

