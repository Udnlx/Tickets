<?php namespace ProcessWire;

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

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
        'booking_sum' => $reserv_seat_item->booking_sum,
        'confirm' => $reserv_seat_item->confirm,
        "id_station"=>$reserv_seat_item->id_station,
        "station"=>$reserv_seat_item->name_station,
        "id_passenger"=>$reserv_seat_item->id_passenger,
        'passenger' => $reserv_seat_item->passenger,
        "type_ticket"=>$reserv_seat_item->type_ticket,
        'passenger_doc' => $reserv_seat_item->passenger_doc,
        'operator' => $reserv_seat_item->operator,
        "comment"=>$reserv_seat_item->comment
        );
}
// echo '<pre>';
// print_r($arr_reserv_seat);
// echo '</pre>';

$bus = $pages->get('id=' . $selected_id_bus . '');
$bus_stations = $bus->station_start;
$list = '';
foreach ($bus_stations as $bus_station) {
    $list .= '<p class="reestr_seat_item" style="font-weight:700; margin: 0; background-color: #b1ff70;">' . $bus_station->title . '</p>';
    $list .= '
        <table style="width: 100%; text-align:center;">
            <thead>
                <tr>
                    <th style="line-height: 0.8; font-size: 10px; width: 5%;">Место</th>
                    <th style="line-height: 0.8; font-size: 10px; width: 10%;">Статус</th>
                    <th style="line-height: 0.8; font-size: 10px; width: 10%;">Сумма<br>к оплате</th>
                    <th style="line-height: 0.8; font-size: 10px; width: 25%;">Пассажир</th>
                    <th style="line-height: 0.8; font-size: 10px; width: 7%;">Тип<br>билета</th>
                    <th style="line-height: 0.8; font-size: 10px; width: 7%;">Дата<br>рождения</th>
                    <th style="line-height: 0.8; font-size: 10px; width: 20%;">Документ</th>
                    <th style="line-height: 0.8; font-size: 10px; width: 16%;">Комментарий</th>
                </tr>
            </thead>
            <tbody>
    ';

    // // Старый метод по наименованию станции
    // foreach ($arr_reserv_seat as $key => $val) {
    //     $bus_station_title = str_replace(' ', '', $bus_station->title);
    //     $bus_old_station_title = str_replace(' ', '', $bus_station->old_station_name);
    //     $ticket_station_title = str_replace(' ', '', $val['station']);
    //     if ($bus_station_title == $ticket_station_title || $bus_old_station_title == $ticket_station_title) {
    //         $page_passenger = $pages->get('template=passengers, id=' . $val['id_passenger'] . '');
    //         $booking_sum = '';
    //         if ($val['booking_sum'] != '') {
    //             $booking_sum = $val['booking_sum'];
    //         }
    //         $list .= '
    //             <tr>
    //                 <td style="padding: 0px 10px;">' . $val['seat'] . '</td>
    //                 <td style="padding: 0px 10px;">' . $val['pay_or_booking'] . '</td>
    //                 <td style="padding: 0px 10px;">' . $booking_sum . '</td>
    //                 <td style="padding: 0px 10px;">' . $val['passenger'] . '</td>
    //                 <td style="padding: 0px 10px;">' . $val['type_ticket'] . '</td>
    //                 <td style="padding: 0px 10px;">' . $page_passenger->birthday_passenger . '</td>
    //                 <td style="padding: 0px 10px;">' . $page_passenger->type_doc_passenger . ' ' . $page_passenger->passport_passenger . ' ' . $page_passenger->num_doc_passenger . '</td>
    //             </tr>
    //         ';
    //     }
    // }
    // // Старый метод по наименованию станции

    // Новый метод по id станции
    foreach ($arr_reserv_seat as $key => $val) {
        $bus_station_id = $bus_station->id;
        $ticket_station_id = $val['id_station'];
        if ($bus_station_id == $ticket_station_id) {
            $page_passenger = $pages->get('template=passengers, id=' . $val['id_passenger'] . '');
            $booking_sum = '';
            if ($val['booking_sum'] != '') {
                $booking_sum = $val['booking_sum'];
            }
            $list .= '
                <tr>
                    <td style="padding: 0px 10px;">' . $val['seat'] . '</td>
                    <td style="padding: 0px 10px;">' . $val['pay_or_booking'] . '</td>
                    <td style="padding: 0px 10px;">' . $booking_sum . '</td>
                    <td style="padding: 0px 10px;">' . $val['passenger'] . '</td>
                    <td style="padding: 0px 10px;">' . $val['type_ticket'] . '</td>
                    <td style="padding: 0px 10px;">' . $page_passenger->birthday_passenger . '</td>
                    <td style="padding: 0px 10px;">' . $page_passenger->type_doc_passenger . ' ' . $page_passenger->passport_passenger . ' ' . $page_passenger->num_doc_passenger . '</td>
                    <td style="padding: 0px 10px;font-size:10px;">' . $val['comment'] . '</td>
                </tr>
            ';
        }
    }
    // Новый метод по id станции

    $list .= '
            </tbody>
        </table>
    ';
    $list .= '<hr>';
    $list .= '<br>';
}
//echo $list;

$reestr_seat = '
<style type="text/css">
tr:nth-child(even) {
    background: #e1e1e1;
}
</style>
<h3>Реестр занятых мест по маршруту<br>' . $selected_bus . '<br>' . $selected_date . ' ' . $selected_time . '</h3>';
$reestr_seat .= $list;
?>

<div id="content" style="max-width: 700px;">
    <h1 class="uk-heading-hero uk-text-center">Печать отчета по рейсу для водителя</h1>
    
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
        <h4 class="uk-margin-remove">Отчет успешно сформирован для печати</h4>
        <h4 class="uk-margin-remove">Выбранный рейс:<br><span style="font-weight: 700;"><?php echo $selected_bus; ?></span></h4>
        <h4 class="uk-margin-remove">Дата: <span style="font-weight: 700;"><?php echo $selected_date; ?></span> отправление<span style="font-weight: 700;"><?php echo $selected_time; ?></span></h4>

        <?php //echo $list; ?>

        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>

<?php   
}
?>

<?php
$pdf = $modules->get('WirePDF');
$pdf->markupMain = $reestr_seat;
$pdf->pageOrientation = 'L';
$pdf->pageFormat = 'A4';
//$pdf->save('my-pdf-file.pdf');
$pdf->download('Для водителя - ' . $selected_bus . ' - ' . $selected_date . ' ' . $selected_time . '');
?>