<?php

namespace ProcessWire;

$postData = file_get_contents('php://input');
$data = json_decode($postData, true);

if (isset($data['idBus'])) {
	$bus = $pages->get("template=buses_item, id=" . $data['idBus'] . "");
	if ($bus->id) {

		//Получение и обработка данных
		$forreg_bus = $bus->title;

		$forreg_id_bus = $data['idBus'];

		$forreg_date_departure = $data['dateDeparture'];

		$time_departure = $bus->option_bus;
		$forreg_time_departure = mb_substr($time_departure, -7, 7);

		$forreg_id_ss = $data['idStationStart'];
		$page_ss = $pages->get("id=" . $forreg_id_ss . "");
		$forreg_ss_name = $page_ss->title;

		$forreg_id_sf = $data['idStationFinish'];
		$page_sf = $pages->get("id=" . $forreg_id_sf . "");
		$forreg_sf_name = $page_sf->title;

		$seat = $data['seat'];
		$seat_padded = sprintf("%02d", $seat);
		$forreg_seat = $seat_padded;

		$forreg_pay_or_booking = "оплачено";

		$forreg_booking_sum = "";

		$forreg_confirm = "";

		$type_ticket = $data['typeTicket'];
		if ($type_ticket == "adult") {
			$forreg_type_ticket = "взрослый";
		}
		if ($type_ticket == "child") {
			$forreg_type_ticket = "детский";
		}

		$passenger_page = $pages->get("template=passengers, title=" . $data['passenger'] . ", birthday_passenger=" . $data['birthdayPassenger'] . ", passport_passenger=" . $data['passengerDocSerial'] . ", num_doc_passenger=" . $data['passengerDocNumber'] . "");
		if ($passenger_page->id) {
			$forreg_passenger = $data['passenger'];
			$forreg_id_passenger = $passenger_page->id;
			$forreg_passenger_doc = $data['passengerDoc'] . ' ' . $data['passengerDocSerial'] . ' ' . $data['passengerDocNumber'];
		} else {
			$pages->add('passengers', '/passazhiry/', [
            'title' => $data['passenger'],
            'name_passenger' => $data['passenger'],
            'gender_passenger' => $data['genderPassenger'],
            'birthday_passenger' => $data['birthdayPassenger'],
            'type_doc_passenger' => $data['passengerDoc'],
            'passport_passenger' => $data['passengerDocSerial'],
            'num_doc_passenger' => $data['passengerDocNumber'],
            'phone_passenger' => $data['passengerPhone'],
            'agent' => $data['passengerCreate'],
	        ]);
	        $new_passenger_page = $pages->get("template=passengers, title=" . $data['passenger'] . ", birthday_passenger=" . $data['birthdayPassenger'] . ", passport_passenger=" . $data['passengerDocSerial'] . ", num_doc_passenger=" . $data['passengerDocNumber'] . "");
	        $new_passenger_page_id = $new_passenger_page->id;
	        $log = '';
	        $log .= date('Y-m-d H:i:s') . ' - Добавлен новый пассажир id - ' . $new_passenger_page_id . ' агентом ' . $data['passengerCreate'] . '.   ';
	        $log .= 'Данные добавленного пассажира: ' . $data['passenger'] . ' - ' . $data['genderPassenger'] . ' - ' . $data['birthdayPassenger'] . ' - ' . $data['passengerDoc'] . ' - ' . $data['passengerDocSerial'] . ' - ' . $data['passengerDocNumber'] . ' - ' . $data['passengerPhone'];
	        file_put_contents(__DIR__ . '/../../../log_add_passengers_api.txt', $log . PHP_EOL, FILE_APPEND);
			$forreg_passenger = $data['passenger'];
			$forreg_id_passenger = $new_passenger_page_id;
			$forreg_passenger_doc = $data['passengerDoc'] . ' ' . $data['passengerDocSerial'] . ' ' . $data['passengerDocNumber'];
		}

		$forreg_operator = $data['operator'];

		$forreg_agent_ticket = $data['agentTicket'];

		$forreg_price_ticket = $data['priceTicket'];
		//Получение и обработка данных

		//Ответ
		$result["bus"] = $forreg_bus;
		$result["idBus"] = $forreg_id_bus;
		$result["dateDeparture"] = $forreg_date_departure;
		$result["timeDeparture"] = $forreg_time_departure;
		$result["idStationStart"] = $forreg_id_ss;
		$result["stationStartName"] = $forreg_ss_name;
		$result["idStationFinish"] = $forreg_id_sf;
		$result["stationFinishName"] = $forreg_sf_name;
		$result["seat"] = $forreg_seat;
		$result["payOrBooking"] = $forreg_pay_or_booking;
		$result["bookingSum"] = $forreg_booking_sum;
		$result["confirm"] = $forreg_confirm;
		$result["typeTicket"] = $forreg_type_ticket;
		$result["passenger"] = $forreg_passenger;
		$result["idPassenger"] = $forreg_id_passenger;
		$result["passengerDoc"] = $forreg_passenger_doc;
		$result["operator"] = $forreg_operator;
		$result["agentTicket"] = $forreg_agent_ticket;
		$result["priceTicket"] = $forreg_price_ticket;
		//Ответ

	} else {
		$result = setError("Автобус с таким ID не найден", $result);
	}
} else {
	$result = setError("Не указан ID автобуса", $result);
}