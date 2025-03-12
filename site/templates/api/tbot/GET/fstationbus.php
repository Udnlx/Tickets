<?php

namespace ProcessWire;

$all_station_finish = [];
if ($input->get['id']) {
	$id = $input->get('id');
	$bus_page = $pages->get("template=buses_item, id=" . $id);
	if ($bus_page->station_finish) {
		foreach ($bus_page->station_finish as $station_finish) {
			$all_station_finish[] = [
				"id" => $station_finish->id,
				"name" => $station_finish->title,
			];
		}
		$result["allStationFinish"] = $all_station_finish;
	} else {
		$result = setError('Автобуса с указанным ID не существует', $result, 404);
	}
} else {
	$result = setError('Не верно указан параметр запроса', $result, 404);
}

