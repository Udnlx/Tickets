<?php

namespace ProcessWire;

$postData = file_get_contents('php://input');
$data = json_decode($postData, true);

$ticket_id = 0;
$validation = true;
$message = 'Билет успешно зарегистрирован';

if (isset($data['idBus'])) {
	$bus = $pages->get("template=buses_item, id=" . $data['idBus'] . "");
	if ($bus->id) {

		//Получение и обработка данных
		$forreg_bus = $bus->title;

		$forreg_id_bus = $data['idBus'];

		$date_departure = $data['dateDeparture'];
		$parts = explode('-', $date_departure);
		if(isset($parts[0]) && isset($parts[1]) && isset($parts[2])) {
			$y = strlen($parts[0]);
			$m = strlen($parts[1]);
			$d = strlen($parts[2]);
		    if($y == 4 && $m == 2 && $d == 2) {
		        $forreg_date_departure = $date_departure;
		    } else {
		    	$validation = false;
				$message = '[dateDeparture] неверный формат даты';
		    }
		} else {
	    	$validation = false;
			$message = '[dateDeparture] неверный формат даты';
		}
		if ($date_departure == '') {
			$validation = false;
			$message = '[dateDeparture] значение не должно быть пустым';
		}

		$time_departure = $bus->option_bus;
		$forreg_time_departure = mb_substr($time_departure, -7, 7);

		$forreg_id_ss = $data['idStationStart'];
		$page_ss = $pages->get("id=" . $forreg_id_ss . "");
		if ($page_ss->template != 'repeater_station_start') {
			$validation = false;
			$message = '[idStationStart] не относится к станциям';
		}
		$forreg_ss_name = $page_ss->title;

		$forreg_id_sf = $data['idStationFinish'];
		$page_sf = $pages->get("id=" . $forreg_id_sf . "");
		if ($page_sf->template != 'repeater_station_finish') {
			$validation = false;
			$message = '[idStationFinish] не относится к станциям';
		}
		$forreg_sf_name = $page_sf->title;

		$seat = $data['seat'];
		$seat_padded = sprintf("%02d", $seat);
		$forreg_seat = $seat_padded;
		if ($forreg_seat > 53 || $forreg_seat <= 0) {
			$validation = false;
			$message = '[seat] место не может быть меньше 0 и больше 54';
		}

		$forreg_pay_or_booking = "оплачено";

		$forreg_booking_sum = "";

		$forreg_confirm = "явился";

		$type_ticket = $data['typeTicket'];
		if ($type_ticket == "adult") {
			$forreg_type_ticket = "взрослый";
		}
		if ($type_ticket == "child") {
			$forreg_type_ticket = "детский";
		}
		if ($type_ticket != "adult" && $type_ticket != "child") {
			$validation = false;
			$message = '[typeTicket] значение должно быть только adult или child';
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
		if ($forreg_operator == '') {
			$validation = false;
			$message = '[operator] значение не должно быть пустым';
		}

		$forreg_agent_ticket = $data['agentTicket'];
		if ($forreg_agent_ticket == '') {
			$validation = false;
			$message = '[agentTicket] значение не должно быть пустым';
		}

		$forreg_price_ticket = $data['priceTicket'];
		if ($forreg_price_ticket == '') {
			$validation = false;
			$message = '[priceTicket] не указана цена';
		}
		$forreg_comment = $data['comment'];
		//Получение и обработка данных

		//Проверка места и регистрация билета
		if ($validation == true) {
			$reserv_seat = $pages->find('template=purchased_tickets, id_bus=' . $data['idBus'] . ', date_depart=' . $data['dateDeparture'] . ',sort=seat');
			$arr_reserv_seat = [];
			foreach ($reserv_seat as $reserv_seat_item) {
			    $arr_reserv_seat[] = (int)$reserv_seat_item->seat;
			}
			if (!in_array($data['seat'], $arr_reserv_seat)) {
			    $pages->add('purchased_tickets', 1026 , [
			    'title' => $forreg_bus . ' - ' . $forreg_date_departure . ' ' . $forreg_time_departure . ' место-' . $forreg_seat,
			    'bus' => $forreg_bus,
			    'id_bus' => $forreg_id_bus,
			    'date_depart' => $forreg_date_departure,
			    'time_depart' => $forreg_time_departure,
			    'id_station' => $forreg_id_ss,
			    'name_station' => $forreg_ss_name,
			    'id_station_finish' => $forreg_id_sf,
			    'name_station_finish' => $forreg_sf_name,
			    'seat' => $forreg_seat,
			    'pay_or_booking' => $forreg_pay_or_booking,
			    'booking_sum' => $forreg_booking_sum,
			    'confirm' => $forreg_confirm,
			    'type_ticket' => $forreg_type_ticket,
			    'id_passenger' => $forreg_id_passenger,
			    'passenger' => $forreg_passenger,
			    'passenger_doc' => $forreg_passenger_doc,
			    'operator' => $forreg_operator,
			    'agent_ticket' => $forreg_agent_ticket,
			    'price_ticket' => $forreg_price_ticket,
			    'comment' => $forreg_comment,
			    ]);
			    $ticket_page = $pages->get('title=' . $forreg_bus . ' - ' . $forreg_date_departure . ' ' . $forreg_time_departure . ' место-' . $forreg_seat . '');
				$ticket_id = $ticket_page->id;

			    $log = '';
			    $log .= date('Y-m-d H:i:s') . ' - Зарегистрирован новый билет id - ' . $ticket_id . ' оператором ' . $forreg_operator . '.   ';
			    $log .= 'Данные билета: ' . $ticket_page->title . ' - ' . $forreg_passenger;
			    file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);

			    $result["message"] = $message;
			    $result["idTicket"] = $ticket_id;
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
				$result["comment"] = $forreg_comment;
			} else {
				$result = setError("Текущее место занято, регистрация не возможна", $result);
				$error_for_log = json_encode($data, JSON_UNESCAPED_UNICODE);
        	    $log = '';
        	    $log .= date('Y-m-d H:i:s') . ' - Была попытка регистрации билета оператором ' . $data['operator'] . '; ';
        	    $log .= 'Ошибка: ' . $result["error"] . '; ';
        	    $log .= 'Данные при регистрации: ' . $error_for_log;
        	    file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);
			}
		} else {
			$result = setError("Ошибка в приходящих данных, регистрация не возможна", $result);
			$result["message"] = $message;
			$error_for_log = json_encode($data, JSON_UNESCAPED_UNICODE);
    	    $log = '';
    	    $log .= date('Y-m-d H:i:s') . ' - Была попытка регистрации билета оператором ' . $data['operator'] . '; ';
    	    $log .= 'Ошибка: ' . $result["error"] . '; ';
    	    $log .= 'Сообщение: ' . $result["message"] . '; ';
    	    $log .= 'Данные при регистрации: ' . $error_for_log;
    	    file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);
		}
		//Проверка места и регистрация билета

	} else {
		$result = setError("Автобус с таким ID не найден", $result);
		$error_for_log = json_encode($data, JSON_UNESCAPED_UNICODE);
	    $log = '';
	    $log .= date('Y-m-d H:i:s') . ' - Была попытка регистрации билета оператором ' . $data['operator'] . '; ';
	    $log .= 'Ошибка: ' . $result["error"] . '; ';
	    $log .= 'Данные при регистрации: ' . $error_for_log;
	    file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);
	}
} else {
	$result = setError("Не указан ID автобуса", $result);
	$error_for_log = json_encode($data, JSON_UNESCAPED_UNICODE);
    $log = '';
    $log .= date('Y-m-d H:i:s') . ' - Была попытка регистрации билета оператором ' . $data['operator'] . '; ';
    $log .= 'Ошибка: ' . $result["error"] . '; ';
    $log .= 'Данные при регистрации: ' . $error_for_log;
    file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);
}