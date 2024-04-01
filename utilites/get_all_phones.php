<?php

namespace ProcessWire;

require_once 'index.php';

ini_set('max_execution_time', 0);
ini_set('memory_limit', '4096M');

echo 'Get all Phones' . '<br>';

$all_passengers = $pages->find('template=passengers');

$all_phones = [];

foreach ($all_passengers as $passenger) {
	echo $passenger->name_passenger . ' - ' . $passenger->phone_passenger . '<br>';

	$all_phones[] = array(
        'phone' => $passenger->phone_passenger,
        );
}

// $title = array
// (
// 'Все телефоны',
// '',
// );

// $headers = array(
//     array(
//         'phone' => 'Телефон',
//     ),    
// );

// header('Content-Type: text/csv; charset=utf-8' );
// header(sprintf( 'Content-Disposition: attachment; filename=Все телефоны' ) );
// header('Content-Transfer-Encoding: binary');
// header('Expires: 0');
// header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
// header('Pragma: public'); 

// $buffer = fopen('php://output', 'w');
// foreach ($title as $line) {
//     $line = mb_convert_encoding($line, 'windows-1251', 'utf-8');
//     fputcsv($buffer,explode(',',$line));
// }
// foreach($headers as $val) { 
//     $val = mb_convert_encoding($val, 'windows-1251', 'utf-8');
//     fputcsv($buffer, $val, ';'); 
// } 
// foreach($all_phones as $val) { 
//     $val = mb_convert_encoding($val, 'windows-1251', 'utf-8');
//     fputcsv($buffer, $val, ';'); 
// } 
// fclose($buffer); 
// exit();