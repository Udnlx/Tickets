<?php

namespace ProcessWire;

require_once 'index.php';

ini_set('max_execution_time', 0);
ini_set('memory_limit', '4096M');

echo 'Delete all Tickets' . '<br>';

$all_tickets = $pages->find('template=purchased_tickets, limit=1000');

foreach ($all_tickets as $ticket) {
	echo $ticket->title . '<br>';
	$del_page = $ticket;
	$pages->delete($del_page);
}
