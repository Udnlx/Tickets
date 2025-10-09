<?php

namespace ProcessWire;

$arr_bus_calendar = [];
if ($input->get['bus']) {
	$id_bus = $input->get('bus');
	$bus_page = $pages->get("template=buses_item, id=" . $id_bus);
	if ($bus_page->title) {
		$year = date('Y');
		$year_plus = strtotime('+1 year', strtotime($year));
		$year_plus = date('Y', $year_plus);
		foreach($bus_page->bus_calendar as $bc_item) {
			if ($bc_item->days && $bc_item->month) {
				$arr_bus_calendar_days = explode(',', $bc_item->days);
				foreach ($arr_bus_calendar_days as $day) {
					$arr_bus_calendar[] = $day . '.' . $bc_item->month . '.' . $year;
					$arr_bus_calendar[] = $day . '.' . $bc_item->month . '.' . $year_plus;
				}
			}
		}
		$result["calendar"] = $arr_bus_calendar;
	} else {
		$result = setError('Автобуса с указанным ID не существует', $result, 404);
	}
} else {
	$result = setError('Не достаточно параметров для запроса', $result, 404);
}