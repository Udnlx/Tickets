<?php 

$selected_bus = !empty($_POST['print_station_selected_bus'])?$_POST['print_station_selected_bus']:NULL;  
$selected_id_bus = !empty($_POST['print_station_selected_id_bus'])?$_POST['print_station_selected_id_bus']:NULL;
$selected_date = !empty($_POST['print_station_selected_date'])?$_POST['print_station_selected_date']:NULL;
$selected_time = !empty($_POST['print_station_selected_time'])?$_POST['print_station_selected_time']:NULL;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Печать отчета по рейсу для автовокзала</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php
$reserv_seat = $pages->find('template=purchased_tickets, id_bus=' . $selected_id_bus . ', date_depart=' . $selected_date . ',sort=seat');
//echo '<pre>'; print_r($reserv_seat); echo '</pre>';
$arr_reserv_seat = [];
foreach ($reserv_seat as $reserv_seat_item) {
    $id_passenger = $reserv_seat_item->id_passenger;
    $page_passenger = $pages->get('template=passengers, id=' . $id_passenger . '');
    $arr_reserv_seat[] = array(
        'passenger' => $reserv_seat_item->passenger,
        'birthday_passenger' => $page_passenger->birthday_passenger,
        'type_doc_passenger' => $page_passenger->type_doc_passenger,
        'passport_passenger' => $page_passenger->passport_passenger,
        'num_doc_passenger' => $page_passenger->num_doc_passenger,
        );
}
// echo '<pre>';
// print_r($arr_reserv_seat);
// echo '</pre>';

$title = array
(
'Отчет по маршруту ' . $selected_bus . ' - ' . $selected_date . ' ' . $selected_time,
'',
);

$headers = array(
    array(
        'passenger' => 'ФИО',
        'birthday_passenger' => 'Дата рождения',
        'type_doc_passenger' => 'Тип документа',
        'passport_passenger' => 'Серия',
        'num_doc_passenger' => 'Номер',
    ),    
);

header('Content-Type: text/csv; charset=utf-8' );
header(sprintf( 'Content-Disposition: attachment; filename=Отчет ' . $selected_bus . ' для автовокзала - %s.csv', date( 'dmY-His' ) ) );
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public'); 

$buffer = fopen('php://output', 'w');
foreach ($title as $line) {
    $line = mb_convert_encoding($line, 'windows-1251', 'utf-8');
    fputcsv($buffer,explode(',',$line));
}
foreach($headers as $val) { 
    $val = mb_convert_encoding($val, 'windows-1251', 'utf-8');
    fputcsv($buffer, $val, ';'); 
} 
foreach($arr_reserv_seat as $val) { 
    $val = mb_convert_encoding($val, 'windows-1251', 'utf-8');
    fputcsv($buffer, $val, ';'); 
} 
fclose($buffer); 
exit();
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Печать отчета по рейсу для автовокзала</h1>
	
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
        <h4 class="uk-margin-remove">Отчет успешно сформирован для печати</h4>
        <h4 class="uk-margin-remove">Выбранный рейс:<br><span style="font-weight: 700;"><?php echo $selected_bus; ?></span></h4>
        <h4 class="uk-margin-remove">Дата: <span style="font-weight: 700;"><?php echo $selected_date; ?></span> отправление<span style="font-weight: 700;"><?php echo $selected_time; ?></span></h4>
        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>

<?php   
}
?>