<?php

namespace ProcessWire;

$free_seats = [];
if ($input->get['bus'] && $input->get['data']) {

	$id_bus = $input->get('bus');
	$data = $input->get('data');

	$mass_reserv_seats_page = $pages->get('template=reserv_seats, id_bus=' . $id_bus . ', date_depart=' . $data . '');
	if ($mass_reserv_seats_page->id > 0) {
	    $arr_mass_reserv_seat_agent = explode(',', $mass_reserv_seats_page->mass_reserv_seats_agent);
	    $arr_mass_reserv_seat = explode(',', $mass_reserv_seats_page->mass_reserv_seats);
	} else {
	    $arr_mass_reserv_seat_agent = [0];
	    $arr_mass_reserv_seat = [0];
	}
	$mass_reserv_seat = [];
	foreach ($arr_mass_reserv_seat as $arr_mass_reserv_seat_item) {
	    $mass_reserv_seat[] = (int)$arr_mass_reserv_seat_item;
	}
	foreach ($arr_mass_reserv_seat_agent as $arr_mass_reserv_seat_agent_item) {
	    $mass_reserv_seat[] = (int)$arr_mass_reserv_seat_agent_item;
	}
	$mass_reserv_seat = array_diff($mass_reserv_seat, [0]);
	// echo '<pre>'; print_r($mass_reserv_seat); echo '</pre>';

	$reserv_seat = $pages->find('template=purchased_tickets, id_bus=' . $id_bus . ', date_depart=' . $data . ',sort=seat');
	$arr_reserv_seat = [];
	foreach ($reserv_seat as $reserv_seat_item) {
	    $arr_reserv_seat[] = (int)$reserv_seat_item->seat;
	}
	foreach ($mass_reserv_seat as $mass_reserv_seat_item) {
	    $arr_reserv_seat[] = (int)$mass_reserv_seat_item;
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