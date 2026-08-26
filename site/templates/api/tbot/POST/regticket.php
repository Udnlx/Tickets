<?php

//namespace ProcessWire;
error_reporting(E_ERROR | E_PARSE);

$postData = file_get_contents('php://input');
$data = json_decode($postData, true);

//Отлавливаем дубликат
$log = '';
$log .= date('Y-m-d H:i:s') . ' - Отлавливаем дубликат; ';
file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);
file_put_contents(__DIR__ . '/../../../log_dataticket_api.txt', $log . PHP_EOL, FILE_APPEND);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Некорректный JSON']);
    exit;
}

$requestKey = hash('sha256', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

$lockDir = __DIR__ . '/request_locks';
if (!is_dir($lockDir)) {
    mkdir($lockDir, 0777, true);
}

$lockFile = $lockDir . '/' . $requestKey . '.lock';
$ttl = 10;
$now = time();

$fp = fopen($lockFile, 'c+');
if (!$fp) {
    http_response_code(500);
    echo json_encode(['error' => 'Не удалось открыть lock-файл'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!flock($fp, LOCK_EX)) {
    fclose($fp);
    http_response_code(500);
    echo json_encode(['error' => 'Не удалось установить блокировку'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Читаем текущее содержимое lock-файла
$contents = stream_get_contents($fp);
$lastTime = (int)trim($contents);

// Если запрос был менее 10 секунд назад — дубликат
if ($lastTime > 0 && ($now - $lastTime) < $ttl) {
    flock($fp, LOCK_UN);
    fclose($fp);

    http_response_code(429);
    echo json_encode([
        'status' => 'duplicate',
        'message' => 'Дубликат запроса: менее 10 секунд'
    ], JSON_UNESCAPED_UNICODE);

    $log = '';
    $log .= date('Y-m-d H:i:s') . ' - Дубликат запроса заблокирован; ';
    $log .= 'Данные=' . json_encode($data, JSON_UNESCAPED_UNICODE);
    file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);
    file_put_contents(__DIR__ . '/../../../log_dataticket_api.txt', $log . PHP_EOL, FILE_APPEND);

    exit;
}

// Обновляем timestamp
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, (string)$now);
fflush($fp);

flock($fp, LOCK_UN);
fclose($fp);
//Отлавливаем дубликат

$dataticket_api = json_encode($data, JSON_UNESCAPED_UNICODE);
$log = '';
$log .= date('Y-m-d H:i:s');
$log .= ' Лог пришедших данных в точку regticket_v1: ' . $dataticket_api;
file_put_contents(__DIR__ . '/../../../log_dataticket_api.txt', $log . PHP_EOL, FILE_APPEND);

$go_sb_reg = true;

if ($data['idStationStart'] == 129599) {
    $go_sb_reg = false;
}

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

		$forreg_date = $data['dateDeparture'];

		// СТАНЦИЯ ПОСАДКИ
		$forreg_id_ss = $data['idStationStart'];
		$page_ss = $pages->get("id=" . $forreg_id_ss . "");
		if ($page_ss->template != 'repeater_station_start') {
		    $validation = false;
		    $message = '[idStationStart] не относится к станциям';
		}
		if ($page_ss->next_day) {
		    $ss_date = date('d.m.Y', strtotime($forreg_date . ' +1 day'));
		} else {
		    $ss_date = date('d.m.Y', strtotime($forreg_date));
		}
		$forreg_ss_name = $ss_date . ' ' . $page_ss->title;

		// СТАНЦИЯ ВЫСАДКИ
		$forreg_id_sf = $data['idStationFinish'];
		$page_sf = $pages->get("id=" . $forreg_id_sf . "");
		if ($page_sf->template != 'repeater_station_finish') {
		    $validation = false;
		    $message = '[idStationFinish] не относится к станциям';
		}
		if ($page_sf->next_day) {
		    $sf_date = date('d.m.Y', strtotime($forreg_date . ' +1 day'));
		} else {
		    $sf_date = date('d.m.Y', strtotime($forreg_date));
		}
		$forreg_sf_name = $sf_date . ' ' . $page_sf->title;

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
			$ticket_type_age = "взрослый";
		}
		if ($type_ticket == "child") {
			$forreg_type_ticket = "детский";
			$ticket_type_age = "детский";
		}
		if ($type_ticket != "adult" && $type_ticket != "child") {
			$validation = false;
			$message = '[typeTicket] значение должно быть только adult или child';
		}

		//найти в начале строки один или несколько символов, которые не являются ни буквой, ни цифрой
		$data['passengerDocSerial'] = preg_replace('/^[^\p{L}\p{N}]+/u', '', $data['passengerDocSerial']);
		$data['passengerDocNumber'] = preg_replace('/^[^\p{L}\p{N}]+/u', '', $data['passengerDocNumber']);

		$passenger_page = $pages->get("template=passengers, title=" . $data['passenger'] . ", birthday_passenger=" . $data['birthdayPassenger'] . ", passport_passenger=" . $data['passengerDocSerial'] . ", num_doc_passenger=" . $data['passengerDocNumber'] . "");
		if ($passenger_page->id) {
			$forreg_passenger = $data['passenger'];
			$forreg_id_passenger = $passenger_page->id;
			$forreg_passenger_doc = $data['passengerDoc'] . ' ' . $data['passengerDocSerial'] . ' ' . $data['passengerDocNumber'];
		} else {
			$citizenship_passenger = 'RU';
		    if (isset($data['citizenshipPassenger'])) {
				$citizenship_passenger = $data['citizenshipPassenger'];
				if ($citizenship_passenger == '') {
				    $citizenship_passenger = 'RU';
				}
			}
			$pages->add('passengers', '/passazhiry/', [
            'title' => $data['passenger'],
            'name_passenger' => $data['passenger'],
            'gender_passenger' => $data['genderPassenger'],
            'citizenship_passenger' => $citizenship_passenger,
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
	        $log .= 'Данные добавленного пассажира: ' . $data['passenger'] . ' - ' . $data['genderPassenger'] . ' - ' . $citizenship_passenger . ' - ' . $data['birthdayPassenger'] . ' - ' . $data['passengerDoc'] . ' - ' . $data['passengerDocSerial'] . ' - ' . $data['passengerDocNumber'] . ' - ' . $data['passengerPhone'];
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
		} else {
			$forreg_price_ticket = (int)$forreg_price_ticket;
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

			$all = range(1, 53);
			$arr_free_seat = array_values(array_diff($all, $arr_reserv_seat));
			if (in_array($data['seat'], $arr_reserv_seat)) {
				if (!empty($arr_free_seat)) {
					$seat = $arr_free_seat[0];
					$seat_padded = sprintf("%02d", $seat);
					$forreg_seat = $seat_padded;
				} else {
					$seat = $data['seat'];
					$seat_padded = sprintf("%02d", $seat);
					$forreg_seat = $seat_padded;
				}
			}

			if (!in_array($forreg_seat, $arr_reserv_seat)) {
			    //РЕГЕСТРИРУЕМ БИЛЕТ В 1С//
			    $sb = [];
			    $sb_log = '';

			    if ($go_sb_reg == true) {
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

					    $to = 'Udnlx@yandex.ru, info@niki-group.ru';
					    $subject = 'Ошибка подключения к 1С';

					    $message = "Не удалось подключиться к 1С.\n\n";
					    $message .= "Время: " . date('Y-m-d H:i:s') . "\n";
					    $message .= "Ошибка: " . $errorMessage . "\n";
					    $message .= "Подробности: " . $info_json . "\n";
					    $message .= "Не удалось проверить занятые места в 1С. По этой причине занимаемое место " . $forreg_seat . " может быть занятым в системе 1С, но в ситсеме Tickets посадка пассажира произойдет, так как на данный момент оно числиться свободным. В дальнейшем возможны расхождения.\n";

					    $headers = "From: tickets@info.com\r\n";
					    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

					    mail($to, $subject, $message, $headers);
					}
					//Подключаемся к 1С

					//Получаем ID автобуса
					try{
					    $dataList = $client->getRaces(["dispatchPlaceId"=>$bus->sb_dispatch_place_id,"arrivalPlaceId"=>$bus->sb_arrival_place_id,"date"=>$date_departure]);
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
					//Получаем ID автобуса

					//Получаем свободные места
					try{
					    $dataSeat = $client->getRaceSeats(["raceCode"=>'' . $uid . '']);
					}
					catch (SoapFault $soapFault){
					    $sb_log .=  'Не удалось вызвать функцию getRaceSeats;';
					    $info_json = json_encode($soapFault);
					    $sb_log .=  $info_json;
					}

					$array_seat = json_decode($dataSeat->return, JSON_UNESCAPED_UNICODE);
					//Получаем свободные места

					//Проверяем, свободно ли место которое сейчас будем регистрировать и получаем его ID
					$sb_free_seats = [];
					foreach ($array_seat as $seat) {
					    $str = mb_substr($seat['name'], -2, 2);
					    $str = sprintf("%02d", $str);
					    if ($str == $forreg_seat) {
					        $sb_seat_id = $seat['code'];
					        //echo $sb_seat_id;
					    }
					    $sb_free_seats[] = $str;
					}
					if (in_array("00", $sb_free_seats)) {
					    $sb_log .=  'Регистрация в 1С не прошла, получен нулевой массив мест';
					}
					if (!in_array($forreg_seat, $sb_free_seats)) {
					    $sb_log .=  'Регистрация в 1С не прошла, место уже занято';
					    $sb_log .=  'ID автобуса в 1С:' . $uid;
					    $sb_log .=  'Желаемое место:' . $forreg_seat;
					    $sb_log .=  'Полученные свободные места в 1С:' . $sb_free_seats;
					    $seat_busy = 'on';
					} else {
					    $seat_busy = 'off';
					}
					//Проверяем, свободно ли место которое сейчас будем регистрировать и получаем его ID

					//Регистрируем билет
					if ($seat_busy == 'off') {
					    $sb_birthday = $data['birthdayPassenger'];
					    $old_date_timestamp = strtotime($sb_birthday);
					    $sb_birthday = date('Y-m-d', $old_date_timestamp);
				            $birth_date = $sb_birthday;  
				            $current_date = date('Y-m-d');  
				            $birth_timestamp = strtotime($birth_date);  
				            $current_timestamp = strtotime($current_date);  
				            $diff_seconds = $current_timestamp - $birth_timestamp;  
				            $age_years = $diff_seconds / (60 * 60 * 24 * 365.25);  
				            $age_years = round($age_years);  
				            //echo $age_years;
				        $ticket_type_code = '1#1#1';
				        if ($age_years <= 11) {
				            //echo 'Детский';
				            $ticket_type_code = '38#6#1';
				        } else {
				            //echo 'Взрослый';
				            $ticket_type_code = '1#1#1';
				        }
					    $sb_doc = $data['passengerDoc'];
					    if ($sb_doc == 'Паспорт РФ') {
				            $sb_doc = '1';
				        }
				        if ($sb_doc == 'Свидетельство о рождении') {
				            $sb_doc = '2';
				        }
				        if ($sb_doc == 'Военный билет') {
				            $sb_doc = '3';
				        }
				        if ($sb_doc == 'Паспорт иностранного пассажира') {
				            $sb_doc = '52';
				        }
				        if ($sb_doc == 'Временное удостоверение ОВД') {
				            $sb_doc = '55';
				        }
				        if ($sb_doc == 'Заграничный паспорт РФ') {
				            $sb_doc = '63';
				        }
				        if ($sb_doc == 'Вид на жительство') {
				            $sb_doc = '66';
				        }
					    $sb_docnum = $data['passengerDocNumber'];
					    $sb_docseries = $data['passengerDocSerial'];
					    $sb_passengername = $data['passenger'];
					    $parts_name = explode(' ', $sb_passengername);
					    $sb_gender = $data['genderPassenger'];
					    if ($sb_gender == 'М') {
					        $sb_gender = 'M';
					    }
					    if ($sb_gender == 'Ж') {
					        $sb_gender = 'F';
					    }
					    $sb_citizenship = 'RU';
					    if (isset($data['citizenshipPassenger'])) {
							$sb_citizenship = $data['citizenshipPassenger'];
							if ($sb_citizenship == '') {
							    $sb_citizenship = 'RU';
							}
						}
					    $sb_phone = $data['passengerPhone'];

					    $fr_racecode = $uid;
					    $fr_birthday = $sb_birthday;
					    $fr_doc = $sb_doc;
					    $fr_docnum = $sb_docnum;
					    $fr_docseries = $sb_docseries;
					    $fr_firstname = $parts_name[1];
					    $fr_gender = $sb_gender;
					    $fr_citizenship = $sb_citizenship;
					    $fr_lastname = $parts_name[0];
					    $fr_middlename = $parts_name[2];
					    $fr_phone = $sb_phone;
					    $fr_seatcode = $sb_seat_id;
					    $fr_ticket_type_code = $ticket_type_code;

					    // echo $fr_racecode . '<br>';
					    // echo $fr_birthday . '<br>';
					    // echo $fr_doc . '<br>';
					    // echo $fr_docnum . '<br>';
					    // echo $fr_docseries . '<br>';
					    // echo $fr_firstname . '<br>';
					    // echo $fr_gender . '<br>';
					    // echo $fr_citizenship . '<br>';
					    // echo $fr_lastname . '<br>';
					    // echo $fr_middlename . '<br>';
					    // echo $fr_phone . '<br>';
					    // echo $fr_seatcode . '<br>';
					    // echo $fr_ticket_type_code . '<br>';

					    try{
					        $dataList = $client->bookOrder([
					            "raceCode" => $fr_racecode,
					            'sales' => json_encode([
					                [
					                    'birthday' => $fr_birthday,
					                    'citizenship' => $fr_citizenship,
					                    'docNum' => $fr_docnum,
					                    'docSeries' => $fr_docseries,
					                    'docTypeCode' => $fr_doc,
					                    'firstName' => $fr_firstname,
					                    'gender' => $fr_gender,
					                    'lastName' => $fr_lastname,
					                    'middleName' => $fr_middlename,
					                    'phone' => $fr_phone,
					                    'seatCode' => $fr_seatcode,
					                    'ticketTypeCode' => $fr_ticket_type_code,
					                ]
					            ]),
					        ]);
					    }
					    catch (SoapFault $soapFault){
					        $sb_log .=  'Не удалось вызвать функцию bookOrder;';
					        $info_json = json_encode($soapFault);
					        $sb_log .=  $info_json;
					    }

					    $answer_book_order = json_decode($dataList->return, JSON_UNESCAPED_UNICODE);
					    // echo '<pre>'; 
					    // var_dump($answer_book_order);
					    // echo '</pre>';
					    // echo $answer_book_order['id'];

					    try{
					        $dataList = $client->confirmOrder(["orderId"=>$answer_book_order['id'],"paymentMethod"=>'Безналичный расчет']);
					        $sb_log .= 'Билет успешно зарегистрирован в системе 1С;';
					    }
					    catch (SoapFault $soapFault){
					        $sb_log .=  'Не удалось вызвать функцию confirmOrder;';
					        $sb_log .=  'Пришедший ответ book_order=' . $answer_book_order . ';';
					        $sb_log .=  'Значение orderId=' . $answer_book_order['id'] . ';';
					        $info_json = json_encode($soapFault);
					        $sb_log .=  $info_json;
					    }
					        
					    $answer_confirm_order = json_decode($dataList->return, JSON_UNESCAPED_UNICODE);
					    // echo '<pre>'; 
					    // var_dump($answer_confirm_order);
					    // echo '</pre>';
					    // echo $answer_confirm_order['tickets'][0]['id'];
					}
					//Регистрируем билет
			    } else {
			    	$sb_log .=  'Регистрация билета в 1С запрещена по станции посадки';
			    }

				$sb['sbLog'] = $sb_log;
				$sb['sbIdBus'] = $uid;
				$sb['sbSeatBusy'] = $seat_busy;
				$sb['sbSeatId'] = $sb_seat_id;
				$sb['sbOrderId'] = $answer_book_order['id'];
				$sb['sbTicketId'] = $answer_confirm_order['tickets'][0]['id'];
			    //РЕГЕСТРИРУЕМ БИЛЕТ В 1С//

			    //РЕГЕСТРИРУЕМ БИЛЕТ В СИСТЕМЕ//
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
			    'sb_ticket_id' => $answer_confirm_order['tickets'][0]['id'],
			    ]);
			    $ticket_page = $pages->get('title=' . $forreg_bus . ' - ' . $forreg_date_departure . ' ' . $forreg_time_departure . ' место-' . $forreg_seat . '');
				$ticket_id = $ticket_page->id;

				$departure_passenger = $pages->get('template=passengers, id=' . $forreg_id_passenger . '');
                //echo 'Плюсуем поездку пассажиру ' . $departure_passenger->title;
                $departure_count = intval($departure_passenger->count_travel) + 1;
                $departure_passenger->of(false);
                $departure_passenger->count_travel = $departure_count;
                $departure_passenger->save();
                $log = '';
                $log .= date('Y-m-d H:i:s') . ' - Пассажиру id - ' . $forreg_id_passenger . ', был прибавлен счетчик поездок, агент - ' . $forreg_agent_ticket;
                file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);

				$sb_log = json_encode($sb, JSON_UNESCAPED_UNICODE);
			    $log = '';
			    $log .= date('Y-m-d H:i:s') . ' - Зарегистрирован новый ' . $ticket_type_age . ' билет id - ' . $ticket_id . ' оператором ' . $forreg_operator . '.   ';
			    $log .= 'Данные билета: ' . $ticket_page->title . ' - ' . $forreg_passenger;
			    $log .= 'Лог 1С: ' . $sb_log;
			    file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);
			    //РЕГЕСТРИРУЕМ БИЛЕТ В СИСТЕМЕ//

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
				$result["informationTicket"] = $bus->information_ticket;
				$result["1C"] = $sb;
			} else {
				$result["statusCode"] = 400;
				$result["error"] = "Текущее место занято и свободных мест нет, регистрация не возможна";
				$error_for_log = json_encode($data, JSON_UNESCAPED_UNICODE);
        	    $log = '';
        	    $log .= date('Y-m-d H:i:s') . ' - Была попытка регистрации билета оператором ' . $data['operator'] . '; ';
        	    $log .= 'Ошибка: ' . $result["error"] . '; ';
        	    $log .= 'Данные при регистрации: ' . $error_for_log;
        	    file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);
			}
		} else {
			$result["statusCode"] = 400;
			$result["error"] = "Ошибка в приходящих данных, регистрация не возможна";
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
		//sleep(1);
		$log = '';
		$log .= date('Y-m-d H:i:s');
		$log .= ' Обработка данных закончена';
		file_put_contents(__DIR__ . '/../../../log_dataticket_api.txt', $log . PHP_EOL, FILE_APPEND);

	} else {
		$result["statusCode"] = 400;
		$result["error"] = "Автобус с таким ID не найден";
		$error_for_log = json_encode($data, JSON_UNESCAPED_UNICODE);
	    $log = '';
	    $log .= date('Y-m-d H:i:s') . ' - Была попытка регистрации билета оператором ' . $data['operator'] . '; ';
	    $log .= 'Ошибка: ' . $result["error"] . '; ';
	    $log .= 'Данные при регистрации: ' . $error_for_log;
	    file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);
	}
} else {
	$result["statusCode"] = 400;
	$result["error"] = "Не указан ID автобуса";
	$error_for_log = json_encode($data, JSON_UNESCAPED_UNICODE);
    $log = '';
    $log .= date('Y-m-d H:i:s') . ' - Была попытка регистрации билета оператором ' . $data['operator'] . '; ';
    $log .= 'Ошибка: ' . $result["error"] . '; ';
    $log .= 'Данные при регистрации: ' . $error_for_log;
    file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);
}