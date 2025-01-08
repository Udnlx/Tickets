<?php

namespace ProcessWire;

$all_station_start = [];
if ($input->get['id']) {
	$id = $input->get('id');
	$bus_page = $pages->get("template=buses_item, id=" . $id);
	if ($bus_page->station_start) {
		foreach ($bus_page->station_start as $station_start) {
			$all_station_start[] = [
				"id" => $station_start->id,
				"name" => $station_start->title,
			];
		}
		$result["all_station_start"] = $all_station_start;
	} else {
		$result = setError('Автобуса с указанным ID не существует', $result, 404);
	}
} else {
	$result = setError('Не верно указан параметр запроса', $result, 404);
}
