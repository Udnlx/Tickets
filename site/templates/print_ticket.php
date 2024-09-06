<?php 

error_reporting(E_ALL);
ini_set('display_errors', 'Off'); 

$ticket_id = !empty($_POST['print_ticket_id'])?$_POST['print_ticket_id']:NULL;  

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
    <h1 class="uk-heading-hero uk-text-center">Печать билета</h1>
    
                
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php
$ticket = $pages->get('id=' . $ticket_id . '');
$passenger = $pages->get('id=' . $ticket->id_passenger . '');
$date_depart = date("d.m.Y",strtotime($ticket->date_depart));

$id_bus = $ticket->id_bus;
$bus = $pages->get('id=' . $id_bus . '');
// $station_list = '';
// if (count($bus->children()) > 0) {
//     $stations = $bus->children();
//     foreach ($stations as $station) {
//         $station_list .= '
//             <p>' . $station->title . '</p>
//         ';
//     }
// }
$station_list = $bus->route;

$content = '
<style type="text/css">
* {
  /*font-family: Helvetica, sans-serif;*/
  font-family: "DejaVu Sans", sans-serif;
}

.logo_ticket {
    position: absolute;}

.pdf-big {
    margin: 20px 0 0 0;
    font-size: 20px;}

.maintext {
    margin: 0;
    font-size: 25px;
    text-align: right;}

.textheader {
    text-align: right;}

.textheader_last {
    text-align: right;
    margin: 0 0 30px 0;}

p {
    margin: 0 0 5px 0;}

.smalltext p {
    margin 0;
    font-size: 12px;
    line-height: 1;}
</style>
';

$array_param_start = preg_split('/[—]/u', $ticket->name_station, -1, PREG_SPLIT_NO_EMPTY);
//print_r($array_param_start);
$array_param_finish = preg_split('/[—]/u', $ticket->name_station_finish, -1, PREG_SPLIT_NO_EMPTY);
//print_r($array_param_finish);
$ticket_date = date("d.m.Y H:m:s", $ticket->created);

$start_station = '';
if ($array_param_start[1]) {
    $start_station = '
    <p>Станция посадки: ' . $array_param_start[0] . ' ' . $date_depart . '</p>
    <p style="margin: -10px 0 0 0;font-size: 12px;">' . $array_param_start[1] . '</p>
    ';
} else {
    $start_station = '
    <p>Станция посадки: ' . $array_param_start[0] . ' ' . $date_depart . '</p>
    ';
}

$finish_station = '';
if ($array_param_finish[1]) {
    $finish_station = '
    <p>Станция высадки: ' . $array_param_finish[0] . '</p>
    <p style="margin: -10px 0 0 0;font-size: 12px;">' . $array_param_finish[1] . '</p>
    ';
} else {
    $finish_station = '
    <p>Станция высадки: ' . $array_param_finish[0] . '</p>
    ';
}

$content .= '
<img class="logo_ticket" src="http://tickets/site/assets/images/Logo_OlimpTickets.png" alt="">
<p class="maintext">ОЛИМП</p>
<p class="textheader">г. Люберцы, ул. Комсомольская, 15</p>
<p class="textheader_last">тел: 8(926)947-55-55</p>

<h2 style="margin: 50px 0 20px 0;">Билет №' . $ticket->id . ' от ' . $ticket_date . '</h2>

<!-- <p>Автобус: ' . $ticket->bus . '</p> -->
<p>Перевозчик: ОЛИМП</p>
<p>Статус билета: ' . $ticket->pay_or_booking . '</p>
<p>Цена билета: ' . $ticket->price_ticket . ' руб.</p>

<p class="pdf-big">О РЕЙСЕ:</p>
<!-- <p>Отправление со станции ' . $ticket->name_station . ' ' . $date_depart . '</p> -->
<p>Место № ' . $ticket->seat . '</p>
' . $start_station . '
' . $finish_station . '
<!-- <p>Цена: [price] руб.</p> -->

<p class="pdf-big">О ПАССАЖИРЕ:</p>
<p>Ф.И.О.: ' . $passenger->name_passenger . '</p>
<p>Вид документа: ' . $passenger->type_doc_passenger . '</p>
<p>Номер документа: ' . $passenger->passport_passenger . ' '. $passenger->num_doc_passenger .'</p>
<p>Дата рождения: ' . $passenger->birthday_passenger . '</p>
<p>Телефон: ' . $passenger->phone_passenger . '</p>

<p class="pdf-big">ДОПОЛНИТЕЛЬНАЯ ИНФОРМАЦИЯ:</p>
<div class="smalltext">
    <p>По предъявлению билета взять бесплатные бирки на багаж.</p>
    <br>
    <p>Наша компания Олимп Осуществляет рейсы по следующим маршрутам:</p>
    <p>Москва – Таганрог</p>
    <p>Москва – Мариуполь</p>
    <p>Москва – Луганск</p>
    <p>Москва  - Алчевск</p>
    <p>Москва – Стаханов</p>
    <br>
    <p><strong>Заказ Билетов не выходя из дома</strong></p>
    <p>+7 (926) 947-55-55</p>
    <p>+7 (959) 276-48-12</p>
    <p>+7 (916) 021-30-05</p>
    <br>
    <!--
    <p><strong>Наше расписание:</strong></p>
    <p>' . $station_list . '</p>
    <br>
    -->
    <p>Пришлем электронный билет Вам на телефон (Вотсап, Телеграм).</p>
    <p>Оплата в Автобусе наличными водителю или на сайте olimp-tickets.ru</p>
</div>
';

include_once __DIR__ . '/dompdf/autoload.inc.php';
$dompdf = new Dompdf\Dompdf();
$dompdf->set_option('isRemoteEnabled', TRUE);
$dompdf->setPaper('A4', 'portrait');
$dompdf->loadHtml($content, 'UTF-8');
$dompdf->render();
 
//Вывод файла в браузер:
//$dompdf->stream('Билет - ' . $ticket_id . ''); 
?>

<div id="content" style="max-width: 700px;">
    <h1 class="uk-heading-hero uk-text-center">Печать билета</h1>
    
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
        <h4 class="uk-margin-remove">Билет успешно сформирован для печати</h4>
        <h4 class="uk-margin-remove">ID билета:<br><span style="font-weight: 700;"><?php echo $ticket_id; ?></span></h4>
        <h4 class="uk-margin-remove">Маршрут:</h4>
        <?php echo $station_list; ?>
        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>

<?php echo $content; ?>

<?php   
}
?>