<?php 

$selected_bus = !empty($_POST['print_selected_bus'])?$_POST['print_selected_bus']:NULL;  
$selected_id_bus = !empty($_POST['print_selected_id_bus'])?$_POST['print_selected_id_bus']:NULL;
$selected_date = !empty($_POST['print_selected_date'])?$_POST['print_selected_date']:NULL;
$selected_time = !empty($_POST['print_selected_time'])?$_POST['print_selected_time']:NULL;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Печать отчета по рейсу для водителя</h1>
	
	            
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
//echo '<pre>'; print_r($arr_reserv_seat); echo '</pre>';
$arr_reserv_seat = [];
foreach ($reserv_seat as $reserv_seat_item) {
    $arr_reserv_seat[] = array(
        'seat' => $reserv_seat_item->seat,
		'pay_or_booking' => $reserv_seat_item->pay_or_booking,
		'confirm' => $reserv_seat_item->confirm,
        "station"=>$reserv_seat_item->name_station,
		"id_passenger"=>$reserv_seat_item->id_passenger,
		'passenger' => $reserv_seat_item->passenger,
		'passenger_doc' => $reserv_seat_item->passenger_doc,
		'operator' => $reserv_seat_item->operator,
        );
}
// echo '<pre>';
// print_r($arr_reserv_seat);
// echo '</pre>';

$bus = $pages->get('id=' . $selected_id_bus . '');
$bus_stations = $bus->children();
$list = '';
foreach ($bus_stations as $bus_station) {
    $list .= '<p class="reestr_seat_item" style="font-weight:700;">' . $bus_station->title . '</p>';
    foreach ($arr_reserv_seat as $key => $val) {
        if ($bus_station->title == $val['station']) {
            $list .= '
            <p class="reestr_seat_item">Место - ' . $val['seat'] . ' - ' . $val['pay_or_booking'] . '<br>' . $val['passenger'] . '</p>
            ';
        }
    }
    $list .= '<br>';
}
//echo $list;

$reestr_seat = '
<style type="text/css">
* {
  /*font-family: Helvetica, sans-serif;*/
  font-family: "DejaVu Sans", sans-serif;
}
</style>
<h3>Реестр занятых мест по маршруту<br>' . $selected_bus . '<br>' . $selected_date . ' ' . $selected_time . '<h3>';
$reestr_seat .= $list;

include_once __DIR__ . '/dompdf/autoload.inc.php';
$dompdf = new Dompdf\Dompdf();
$dompdf->set_option('isRemoteEnabled', TRUE);
$dompdf->setPaper('A4', 'portrait');
$dompdf->loadHtml($reestr_seat, 'UTF-8');
$dompdf->render();
 
// Вывод файла в браузер:
$dompdf->stream('Для водителя - ' . $selected_bus . ' - ' . $selected_date . ' ' . $selected_time . ''); 
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Печать отчета по рейсу для водителя</h1>
	
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