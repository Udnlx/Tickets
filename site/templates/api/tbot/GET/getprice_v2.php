<?php

namespace ProcessWire;

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

	if ($bus_page->title) {

		$station_start = $bus_page->station_start->get("id=" . $id_sstation);
		if ($station_start) {
			$station_start_title = preg_split('/[—]/u', $station_start->title, -1, PREG_SPLIT_NO_EMPTY);
			$start_title = trim($station_start_title[0]);
		} else {
			$start_title = '';
		}

		$station_finish = $bus_page->station_finish->get("id=" . $id_fstation);
		if ($station_finish) {
			$station_finish_title = preg_split('/[—]/u', $station_finish->title, -1, PREG_SPLIT_NO_EMPTY);
			$finish_title = trim($station_finish_title[0]);
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

		$received_price = 5000;
		if ($age == 'child') {
			$received_price = $received_price/2;
			$received_price = $received_price + $extra_price;
		} else {
			$received_price = 5000;
			$received_price = $received_price + $extra_price;
		}

		foreach ($bus_page->table_price as $item_price) {
			if ($item_price->name_station == $start_title && $item_price->name_station_finish == $finish_title) {
				$received_price = $item_price->price_ticket/1;
				if ($age == 'child') {
					$received_price = $received_price/2;
				}
				$received_price = $received_price + $extra_price;
				break;
			} else {
				$received_price = 5000;
				if ($age == 'child') {
					$received_price = $received_price/2;
				}
				$received_price = $received_price + $extra_price;
			}
		}

		$price = $received_price;

		$result["price"] = $price;

	} else {
		$result = setError('Автобуса с указанным ID не существует', $result, 404);
	}

} else {
	$result = setError('Не достаточно параметров для запроса', $result, 404);
}