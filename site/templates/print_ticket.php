<?php 

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

$content .= '
<img class="logo_ticket" src="http://tickets/site/assets/images/Logo_OlimpTickets.png" alt="">
<p class="maintext">ОЛИМП</p>
<p class="textheader">г. Люберцы, ул. Комсомольская, 15</p>
<p class="textheader_last">тел: 8(926)947-55-55</p>

<h2 style="margin: 50px 0 20px 0;">Билет</h2>

<p>Автобус: ' . $ticket->bus . '</p>
<p>Перевозчик: ОЛИМП</p>
<p>Статус билета: ' . $ticket->pay_or_booking . '</p>
<p>Цена билета: ' . $ticket->price_ticket . ' руб.</p>

<p class="pdf-big">О РЕЙСЕ:</p>
<p>Отправление со станции ' . $ticket->name_station . ' ' . $date_depart . '</p>
<p>Место № ' . $ticket->seat . '</p>
<!-- <p>Цена: [price] руб.</p> -->

<p class="pdf-big">О ПАССАЖИРЕ:</p>
<p>Ф.И.О.: ' . $passenger->name_passenger . '</p>
<p>Вид документа: ' . $passenger->type_doc_passenger . '</p>
<p>Номер документа: ' . $passenger->passport_passenger . ' '. $passenger->num_doc_passenger .'</p>
<p>Дата рождения: ' . $passenger->birthday_passenger . '</p>
<p>Телефон: ' . $passenger->phone_passenger . '</p>

<p class="pdf-big">ДОПОЛНИТЕЛЬНАЯ ИНФОРМАЦИЯ:</p>
<div class="smalltext">
    <p>Уважаемый пассажир <strong>вам нужно быть за 30 мин до отправления на автовокзале</strong>, в любой кассе вы можете взять бесплатные бирки для багажа при предъявлении этого билета.</p>
    <br>
    <p><strong>Если у вас возникли вопросы звоните по нашим телефонам:</strong></p>
    <p>+7 (926) 947-55-55 (Вотсап, телеграмм)</p>
    <p>+7 (959) 276-48-12</p>
    <br>
    <p><strong>Наше расписание:</strong></p>
    <p>Из Москвы автовокзал Саларьево 21:00 и 17:00</p>
    <p>Из Стаханова – 15:30 и 17:30</p>
    <p>Из Алчевска – 16:30 и 18:30</p>
    <p>Из Луганска ЖД- 17:20 и 19:20</p>
    <p>Из Луганска Автовокзала – 17:50 и 19:50</p>
    <br>
    <p>Пришлем ваш электронный билет Вам на телефон (Viber,WhatsApp,СМС, Телеграм) также подсказку (какой номер автобуса и перрон)</p>
    <p>Оплата в автобусе или на сайте : olimp-tickets.ru</p>
</div>
';

include_once __DIR__ . '/dompdf/autoload.inc.php';
$dompdf = new Dompdf\Dompdf();
$dompdf->set_option('isRemoteEnabled', TRUE);
$dompdf->setPaper('A4', 'portrait');
$dompdf->loadHtml($content, 'UTF-8');
$dompdf->render();
 
// Вывод файла в браузер:
$dompdf->stream('Билет - ' . $ticket_id . ''); 
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Печать билета</h1>
	
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
        <h4 class="uk-margin-remove">Билет успешно сформирован для печати</h4>
        <h4 class="uk-margin-remove">ID билета:<br><span style="font-weight: 700;"><?php echo $ticket_id; ?></span></h4>
        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>

<?php   
}
?>