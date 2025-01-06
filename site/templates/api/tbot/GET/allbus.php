<?php

namespace ProcessWire;

$bus_pages = $pages->find("template=buses_item, sort=sort");
$all_bus = [];
foreach ($bus_pages as $bus) {
	$all_bus[] = [
		"id" => $bus->id,
		"name" => $bus->title,
	];
}

$result["all_bus"] = $all_bus;