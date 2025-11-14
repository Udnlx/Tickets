<?php

namespace ProcessWire;

require_once '../index.php';

ini_set('max_execution_time', 0);
ini_set('memory_limit', '4096M');

echo 'Search Tickets' . '<br>';

$all_tickets = $pages->find('template=purchased_tickets, passenger%=Тесто');

// echo $all_tickets;

foreach ($all_tickets as $ticket) {
	echo 'ID билета: ' . $ticket->id . '<br>';
	echo 'Пассажир: ' . $ticket->passenger . '<br>';
	echo 'ID пассажира: ' . $ticket->id_passenger . '<br>';
	echo '<br>';
}