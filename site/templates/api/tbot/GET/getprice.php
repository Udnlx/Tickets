<?php

namespace ProcessWire;

$price = 0;
if ($input->get['bus'] && $input->get['sstation'] && $input->get['fstation'] && $input->get['age']) {

	$id_bus = $input->get('bus');
	$id_sstation = $input->get('sstation');
	$id_fstation = $input->get('fstation');
	$age = $input->get('age');

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

		$received_price = 5000;
		if ($age == 'child') {
			$received_price = $received_price/2;
		}

		foreach ($bus_page->table_price as $item_price) {
			if ($item_price->name_station == $start_title && $item_price->name_station_finish == $finish_title) {
				$received_price = $item_price->price_ticket/1;
			} else {
				$received_price = 5000;
			}
			if ($age == 'child') {
				$received_price = $received_price/2;
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