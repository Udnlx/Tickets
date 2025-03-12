<?php

namespace ProcessWire;

$free_seats = [];
if ($input->get['bus'] && $input->get['data']) {

	$id_bus = $input->get('bus');
	$data = $input->get('data');

	$reserv_seat = $pages->find('template=purchased_tickets, id_bus=' . $id_bus . ', date_depart=' . $data . ',sort=seat');
	$arr_reserv_seat = [];
	foreach ($reserv_seat as $reserv_seat_item) {
	    $arr_reserv_seat[] = (int)$reserv_seat_item->seat;
	}

	$max_seat = 53;
	$arr_free_seat = [];
	for ($num_seat = 1; $num_seat <= $max_seat; $num_seat++) {
		if (!in_array($num_seat, $arr_reserv_seat)) {
		    $arr_free_seat[] = $num_seat;
		}
	}

	$reserv_seat = $arr_reserv_seat;
	$free_seats = $arr_free_seat;

	$result["reservSeats"] = $reserv_seat;
	$result["freeSeats"] = $free_seats;

} else {
	$result = setError('Не достаточно параметров для запроса', $result, 404);
}