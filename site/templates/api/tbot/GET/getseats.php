<?php

//namespace ProcessWire;
error_reporting(E_ERROR | E_PARSE);

$free_seats = [];
if ($input->get['bus'] && $input->get['data']) {

	$id_bus = $input->get('bus');
	$data = $input->get('data');

	//==================ПОЛУЧАЕМ ЗАНЯТЫЕ МЕСТА В 1С==================//
	//Подключаемся к 1С
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
	    $sb_log .= 'Подключение к 1C прошло успешно;';
	}
	catch (SoapFault $soapFault){
	    $sb_log .=  'Не подключились к 1C;';
	    $info_json = json_encode($soapFault);
	    $sb_log .=  $info_json;
	}
	//Подключаемся к 1С

	//Получаем ID автобуса
	$bus = $pages->get("template=buses_item, id=" . $id_bus . "");
	try{
	    $dataList = $client->getRaces(["dispatchPlaceId"=>$bus->sb_dispatch_place_id,"arrivalPlaceId"=>$bus->sb_arrival_place_id,"date"=>$data]);
	}
	catch (SoapFault $soapFault){
	    $sb_log .=  'Не удалось вызвать функцию по получению ID автобуса;';
	    $info_json = json_encode($soapFault);
	    $sb_log .=  $info_json;
	}
	$array = explode("uid",$dataList->return);

    $sb_bus = $array[1];
    $sb_bus = explode(",",$sb_bus);
    $uid = mb_substr($sb_bus[0], 3);
    $uid = mb_substr($uid, 0, -1);
    $sb_log .= 'ID автобуса в 1С: ' . $uid . ';';
	//Получаем ID автобуса

	//Получаем свободные и занятые места
	try{
	    $dataSeat = $client->getRaceSeats(["raceCode"=>'' . $uid . '']);
	}
	catch (SoapFault $soapFault){
	    $sb_log .=  'Не удалось вызвать функцию;';
	    $info_json = json_encode($soapFault);
	    $sb_log .=  $info_json;
	}

	$array_seat = json_decode($dataSeat->return, JSON_UNESCAPED_UNICODE);

	$sb_free_seats = [];
    foreach ($array_seat as $seat) {
        $str = mb_substr($seat['name'], -2, 2);
        $sb_free_seats[] = $str;
    }

    $sb_max_seat = 51;
    $sb_occupied_seats = [];
    for ($sb_num_seat = 1; $sb_num_seat <= $sb_max_seat; $sb_num_seat++) {
        if (!in_array($sb_num_seat, $sb_free_seats)) {
            $sb_occupied_seats[] = $sb_num_seat;
        }
    }
	//Получаем свободные и занятые места
	//==================ПОЛУЧАЕМ ЗАНЯТЫЕ МЕСТА В 1С==================//





    //==================ПОЛУЧАЕМ ЗАРЕЗЕРВИРОВАННЫЕ МЕСТА В НАШЕЙ СИСТЕМЕ==================//
	$mass_reserv_seats_page = $pages->get('template=reserv_seats, id_bus=' . $id_bus . ', date_depart=' . $data . '');
	if ($mass_reserv_seats_page->id > 0) {
	    $arr_mass_reserv_seat_agent = explode(',', $mass_reserv_seats_page->mass_reserv_seats_agent);
	    $arr_mass_reserv_seat = explode(',', $mass_reserv_seats_page->mass_reserv_seats);
	} else {
	    $arr_mass_reserv_seat_agent = [0];
	    $arr_mass_reserv_seat = [0];
	}
	$mass_reserv_seat = [];
	foreach ($arr_mass_reserv_seat as $arr_mass_reserv_seat_item) {
	    $mass_reserv_seat[] = (int)$arr_mass_reserv_seat_item;
	}
	foreach ($arr_mass_reserv_seat_agent as $arr_mass_reserv_seat_agent_item) {
	    $mass_reserv_seat[] = (int)$arr_mass_reserv_seat_agent_item;
	}
	$mass_reserv_seat = array_diff($mass_reserv_seat, [0]);
	// echo '<pre>'; print_r($mass_reserv_seat); echo '</pre>';
	//==================ПОЛУЧАЕМ ЗАРЕЗЕРВИРОВАННЫЕ МЕСТА В НАШЕЙ СИСТЕМЕ==================//





	//==================ПОЛУЧАЕМ ЗАНЯТЫЕ МЕСТА В НАШЕЙ СИСТЕМЕ И ГРУПИРУЕМ ВСЕ ЗАНЯТОЕ В ОДИН МАССИВ==================//
	$reserv_seat = $pages->find('template=purchased_tickets, id_bus=' . $id_bus . ', date_depart=' . $data . ',sort=seat');
	$arr_reserv_seat = [];
	foreach ($sb_occupied_seats as $sb_occupied_seats_item) {
	    $arr_reserv_seat[] = (int)$sb_occupied_seats_item;
	}
	foreach ($mass_reserv_seat as $mass_reserv_seat_item) {
	    $arr_reserv_seat[] = (int)$mass_reserv_seat_item;
	}
	foreach ($reserv_seat as $reserv_seat_item) {
	    $arr_reserv_seat[] = (int)$reserv_seat_item->seat;
	}
	//==================ПОЛУЧАЕМ ЗАНЯТЫЕ МЕСТА В НАШЕЙ СИСТЕМЕ И ГРУПИРУЕМ ВСЕ ЗАНЯТОЕ В ОДИН МАССИВ==================//





	$max_seat = 53;
	$arr_free_seat = [];
	for ($num_seat = 1; $num_seat <= $max_seat; $num_seat++) {
		if (!in_array($num_seat, $arr_reserv_seat)) {
		    $arr_free_seat[] = $num_seat;
		}
	}

	$reserv_seat = $arr_reserv_seat;
	$free_seats = $arr_free_seat;

	$result["sbLog"] = $sb_log;
	$result["reservSeats"] = $reserv_seat;
	$result["freeSeats"] = $free_seats;

} else {
	$result = setError('Не достаточно параметров для запроса', $result, 404);
}