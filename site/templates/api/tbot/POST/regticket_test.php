<?php

namespace ProcessWire;
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

// $requestKey = md5(json_encode([
//     'operator' => $data['operator'] ?? '',
//     'idBus' => $data['idBus'] ?? '',
//     'dateDeparture' => $data['dateDeparture'] ?? '',
//     'idStationStart' => $data['idStationStart'] ?? '',
//     'idStationFinish' => $data['idStationFinish'] ?? '',
//     'seat' => $data['seat'] ?? '',
//     'passenger' => trim($data['passenger'] ?? ''),
//     'birthdayPassenger' => $data['birthdayPassenger'] ?? '',
//     'passengerDocSerial' => $data['passengerDocSerial'] ?? '',
//     'passengerDocNumber' => $data['passengerDocNumber'] ?? '',
//     'priceTicket' => $data['priceTicket'] ?? '',
// ], JSON_UNESCAPED_UNICODE));

// $lockFile = sys_get_temp_dir() . '/regticket_' . $requestKey . '.lock';
// $lockFp = fopen($lockFile, 'c');

// if (!$lockFp || !flock($lockFp, LOCK_EX | LOCK_NB)) {
//     $result = [
//         'statusCode' => 409,
//         'error' => 'Такой запрос уже обрабатывается'
//     ];

//     $log = '';
//     $log .= date('Y-m-d H:i:s') . ' - Дубликат запроса заблокирован; ';
//     $log .= 'Ключ=' . $requestKey . '; ';
//     $log .= 'Данные=' . json_encode($data, JSON_UNESCAPED_UNICODE);
//     file_put_contents(__DIR__ . '/../../../log_regticket_api.txt', $log . PHP_EOL, FILE_APPEND);

//     header('Content-Type: application/json; charset=utf-8');
//     echo json_encode($result, JSON_UNESCAPED_UNICODE);
//     exit;
// }

//==========================================================
// $start_date = date ('2025-12-25');
// $finish_date = date ('2026-01-11');

// $bus = $pages->get("template=buses_item, id=" . $data['idBus'] . "");
// $date_departure = $data['dateDeparture'];
// $forreg_agent_ticket = $data['agentTicket'];
// $forreg_price_ticket = $data['priceTicket'];

// if ($forreg_price_ticket == '') {
// 	$validation = false;
// 	$message = '[priceTicket] не указана цена';
// } else {
// 	if ((($date_departure >= $start_date) && ($date_departure <= $finish_date))) {
// 		//echo 'Дата входит в диапазон наценки';
// 		if ($forreg_agent_ticket == 'Site') {
// 			$forreg_price_ticket = (int)$forreg_price_ticket;
// 		} else {
// 			$extra_price = $bus->extra_price;
// 			$forreg_price_ticket = (int)$forreg_price_ticket + $extra_price;
// 		}
// 	} else {
// 		//echo 'Дата не входит в диапазон наценки';
// 		$forreg_price_ticket = (int)$forreg_price_ticket; 
// 	}
// }

// $result["priceTicket"] = $forreg_price_ticket;
//==========================================================



//==========================================================
// $seat = $data['seat'];
// $seat_padded = sprintf("%02d", $seat);
// $forreg_seat = $seat_padded;

// $reserv_seat = $pages->find('template=purchased_tickets, id_bus=' . $data['idBus'] . ', date_depart=' . $data['dateDeparture'] . ',sort=seat');
// $arr_reserv_seat = [];
// foreach ($reserv_seat as $reserv_seat_item) {
//     $arr_reserv_seat[] = (int)$reserv_seat_item->seat;
// }
// $all = range(1, 53);
// $arr_free_seat = array_values(array_diff($all, $arr_reserv_seat));
// print_r($arr_reserv_seat);
// print_r($arr_free_seat);

// if (in_array($data['seat'], $arr_reserv_seat)) {
// 	if (!empty($arr_free_seat)) {
// 		$seat = $arr_free_seat[0];
// 		$seat_padded = sprintf("%02d", $seat);
// 		$forreg_seat = $seat_padded;
// 	} else {
// 		$seat = $data['seat'];
// 		$seat_padded = sprintf("%02d", $seat);
// 		$forreg_seat = $seat_padded;
// 	}
// }

// $result["seat"] = $forreg_seat;
//==========================================================



//==========================================================
$bus = $pages->get("template=buses_item, id=" . $data['idBus'] . "");

$result["requestKey"] = $requestKey;
$result["idBus"] = $bus->id;
$result["informationTicket"] = $bus->information_ticket;
//==========================================================



// if (isset($lockFp) && $lockFp) {
//     flock($lockFp, LOCK_UN);
//     fclose($lockFp);
// }