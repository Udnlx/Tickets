<?php

namespace ProcessWire;
error_reporting(E_ERROR | E_PARSE);

// $start_date = date ('2025-12-25');
// $finish_date = date ('2026-01-11');

$start_date = date ('2026-05-08');
$finish_date = date ('2026-05-08');

$price = 0;
if ($input->get['bus'] && $input->get['sstation'] && $input->get['fstation'] && $input->get['age'] && $input->get['departure']) {

	$id_bus = $input->get('bus');
	$id_sstation = $input->get('sstation');
	$id_fstation = $input->get('fstation');
	$age = $input->get('age');
	$departure = $input->get('departure');

	$bus_page = $pages->get("template=buses_item, id=" . $id_bus);

	$arr_extra_calendar = [];
	$calendar_extra_price = 0;
	if (count($bus_page->extra_calendar) > 0) {
	    $year = date('Y');
	    $year_plus = strtotime('+1 year', strtotime($year));
	    $year_plus = date('Y', $year_plus);
	    foreach ($bus_page->extra_calendar as $ec_item) {
	        if ($ec_item->days && $ec_item->month) {
	            $arr_extra_calendar_days = explode(',', $ec_item->days);
	            foreach ($arr_extra_calendar_days as $day) {
	                $arr_extra_calendar[] = [
	                    'date' => $year . '-' . $ec_item->month . '-' . $day,
	                    'extra_price' => $ec_item->extra_price,
	                ];
	                $arr_extra_calendar[] = [
	                    'date' => $year_plus . '-' . $ec_item->month . '-' . $day,
	                    'extra_price' => $ec_item->extra_price,
	                ];
	            }
	        }
	    }
	    $needDate = $input->get['departure'];
	    foreach ($arr_extra_calendar as $row) {
	      if (($row['date'] ?? null) === $needDate) {
	        $calendar_extra_price = $row['extra_price'] ?? null;
	        break;
	      }
	    }
	}
	//echo $calendar_extra_price;

	$arr_prices_piece = [];
	if (count($bus_page->extra_calendar_piece) > 0) {
	    $year = date('Y');
	    $year_plus = strtotime('+1 year', strtotime($year));
	    $year_plus = date('Y', $year_plus);
	    foreach ($bus_page->extra_calendar_piece as $ec_item) {
	        if ($ec_item->days && $ec_item->month) {
	            $arr_extra_calendar_days = explode(',', $ec_item->days);
	            foreach ($arr_extra_calendar_days as $day) {
	                $arr_prices_piece[] = [
	                	'name_station_start' => $ec_item->name_station,
	                	'name_station_finish' => $ec_item->name_station_finish,
	                    'date' => $year . '-' . $ec_item->month . '-' . $day,
	                    'extra_price' => $ec_item->extra_price,
	                ];
	                $arr_prices_piece[] = [
	                	'name_station_start' => $ec_item->name_station,
	                	'name_station_finish' => $ec_item->name_station_finish,
	                    'date' => $year_plus . '-' . $ec_item->month . '-' . $day,
	                    'extra_price' => $ec_item->extra_price,
	                ];
	            }
	        }
	    }
	}
	//echo $arr_prices_piece;

	if ($bus_page->title) {

		$station_start = $bus_page->station_start->get("id=" . $id_sstation);
		if ($station_start) {
			$station_start_title = preg_split('/[—]/u', $station_start->title, -1, PREG_SPLIT_NO_EMPTY);
			$start_title = trim($station_start_title[0]);
			//echo $start_title;
		} else {
			$start_title = '';
		}

		$station_finish = $bus_page->station_finish->get("id=" . $id_fstation);
		if ($station_finish) {
			$station_finish_title = preg_split('/[—]/u', $station_finish->title, -1, PREG_SPLIT_NO_EMPTY);
			$finish_title = trim($station_finish_title[0]);
			//echo $finish_title;
		} else {
			$finish_title = '';
		}

		$extra_price = 0;
		if ((($departure >= $start_date) && ($departure <= $finish_date))) {
			//echo 'Дата входит в диапазон наценки';
			$extra_price = $bus_page->extra_price;
		} else {
			//echo 'Дата не входит в диапазон наценки';
			$extra_price = 0;
		}

		$prices_piece = 0;
		$needDate = $input->get['departure'];
		foreach ($arr_prices_piece as $row) {
		    if (
		        ($row['name_station_start'] ?? '') === $start_title &&
		        ($row['name_station_finish'] ?? '') === $finish_title &&
		        ($row['date'] ?? '') === $needDate
		    ) {
		        $prices_piece = $row['extra_price'] ?? 0;
		        break;
		    }
		}

		$received_price = 5000;
		if ($age == 'child') {
			$received_price = $received_price/2;
			$received_price = $received_price + $extra_price + $calendar_extra_price + $prices_piece;
		} else {
			$received_price = 5000;
			$received_price = $received_price + $extra_price + $calendar_extra_price + $prices_piece;
		}

		foreach ($bus_page->table_price as $item_price) {
			if ($item_price->name_station == $start_title && $item_price->name_station_finish == $finish_title) {
				$received_price = $item_price->price_ticket/1;
				if ($age == 'child') {
					$received_price = $received_price/2;
				}
				$received_price = $received_price + $extra_price + $calendar_extra_price + $prices_piece;
				break;
			} else {
				$received_price = 5000;
				if ($age == 'child') {
					$received_price = $received_price/2;
				}
				$received_price = $received_price + $extra_price + $calendar_extra_price + $prices_piece;
			}
		}

		$price = $received_price;

		//$result["arr_prices_piece"] = $arr_prices_piece;
		$result["calendar_extra_price"] = $calendar_extra_price;
		$result["extra_price"] = $extra_price;
		$result["prices_piece"] = $prices_piece;
		$result["price"] = $price;

	} else {
		$result = setError('Автобуса с указанным ID не существует', $result, 404);
	}

} else {
	$result = setError('Не достаточно параметров для запроса', $result, 404);
}