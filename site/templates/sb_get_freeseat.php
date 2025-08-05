<?php

error_reporting(E_ERROR | E_PARSE);

$sb_log = '<p style="font-weight:700;">Получение свободных мест на автобусе в 1С системе</p>';

$found = false;

// $sb_log .= $sb_dispatch_place_id . '<br>';
// $sb_log .= $sb_arrival_place_id . '<br>';
// $sb_log .= $sb_dispatch_date . '<br>';
// $sb_log .= $sb_dispatch_time . '<br><br>';

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
    $sb_log .= '<p style="color:green;margin:0;">Подключение прошло успешно</p>';
    $switching_on = true;
}
catch (SoapFault $soapFault){
    $sb_log .=  '<p style="color:red;margin:0;">Не подключились</p>';
    $info_json = json_encode($soapFault);
    $sb_log .=  '<p style="color:red;">' . $info_json . '</p>';
    $switching_on = false;
}



try{
    $dataList = $client->getRaces(["dispatchPlaceId"=>$sb_dispatch_place_id,"arrivalPlaceId"=>$sb_arrival_place_id,"date"=>$sb_dispatch_date]);
    $sb_log .=  '<p style="color:green;margin:0;">Функции на сервер отправлены</p>';
}
catch (SoapFault $soapFault){
    $sb_log .=  '<p style="color:red;margin:0;">Не удалось вызвать функцию</p>';
    $info_json = json_encode($soapFault);
    $sb_log .=  '<p style="color:red;">' . $info_json . '</p>';
}

// $dataListjson = json_decode($dataList->return, JSON_UNESCAPED_UNICODE);
// echo '<pre>'; 
// var_dump($dataListjson);
// echo '</pre>';

$array = explode("uid",$dataList->return);



foreach ($array as $array_item) {
    $bus_on = false;
    if (stristr($array_item,'"dispatchDate":"' . $sb_dispatch_date . ' ' . $sb_dispatch_time . '"')!==false) {
        $found = true;
        $sb_bus = $array_item;
        $sb_bus = explode(",",$sb_bus);
        $sb_log .= '<p style="color:green;font-weight:700;text-align:center;">Найден автобус: </p>';
        $sb_log .= '"uid' . $sb_bus[0] . '-' . $sb_bus[3] . '-' . $sb_bus[5] . '<br>';
        $uid = mb_substr($sb_bus[0], 3);
        $uid = mb_substr($uid, 0, -1);
        $sb_log .= '<p style="color:green;font-weight:700;text-align:center;">ID автобуса в системе 1С: </p>';
        $sb_log .= '<p id="sb_idbus">' . $uid . '</p>';
        $sb_log .= '<p style="color:green;font-weight:700;text-align:center;">ID автобуса по станциям посадки и высадки: </p>';
        $sb_log .= '<p id="new_sb_idbus">Автобус не найден</p><br>';
        $bus_on = true;
        break;
    }
}

if ($found == false) {
    $sb_log .=  '<p style="color:red;font-weight:700;text-align:center;">Автобусов в базе 1С не найдено,<br>проверьте параметры у рейса для связки с 1С системой</p>';
    $uid = '';
}

if ($found == true) {
    $sb_log .=  '<p style="color:green;font-weight:700;text-align:center;">Свободные места в автобусе: </p>';

    try{
        $dataSeat = $client->getRaceSeats(["raceCode"=>'' . $uid . '']);
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

    $sb_free_seats = [];
    foreach ($array_seat as $seat) {
        $str = mb_substr($seat['name'], -2, 2);
        $sb_free_seats[] = $str;
    }

    foreach ($sb_free_seats as $sb_free_seat) {
        $sb_log .=  $sb_free_seat . ', ';
    }

    $sb_log .=  '<p style="color:green;font-weight:700;text-align:center;">Занятые места в автобусе: </p>';

    $sb_max_seat = 51;
    $sb_occupied_seats = [];
    for ($sb_num_seat = 1; $sb_num_seat <= $sb_max_seat; $sb_num_seat++) {
        if (!in_array($sb_num_seat, $sb_free_seats)) {
            $sb_occupied_seats[] = $sb_num_seat;
        }
    }

    foreach ($sb_occupied_seats as $sb_occupied_seat) {
        $sb_log .=  $sb_occupied_seat . ', ';
    }
}