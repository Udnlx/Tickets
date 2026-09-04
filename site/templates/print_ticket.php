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

$transporter = 'ООО Олимп';
$header_ticket = '
<img class="logo_ticket" src="https://lk.olimp-tickets.ru/site/assets/images/logo_t.png" alt="">
<p class="maintext">ОЛИМП</p>
<p class="textheader">г. Люберцы, ул. Комсомольская, 15</p>
<p class="textheader_last">тел: 8(926)947-55-55</p>
';

$id_bus = $ticket->id_bus;
if ($id_bus == '39265' || $id_bus == '39273') {
    $transporter = 'ООО Терек';
    $header_ticket = '
    <img class="logo_ticket" src="https://lk.olimp-tickets.ru/site/assets/images/logo_t.png" alt="">
    <p class="maintext">ОЛИМП</p>
    <p class="textheader">г. Люберцы, ул. Комсомольская, 15</p>
    <p class="textheader_last">тел: 8(926)947-55-55</p>
';
}
if ($id_bus == '78777') {
    $transporter = 'АТК';
    $header_ticket = '
    <p class="maintext">АТК</p>
    <p class="textheader_last">тел: 8(925)047-30-30</p>
';
}

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

.smalltext {
    position: relative;}

.smalltext p {
    margin 0;
    font-size: 12px;
    line-height: 1;}

.qr-code p.qr-code-text {
    margin: 0;
    font-size: 14px;}

.qr-links {
    display: flex;
    justify-content: center;}

.qr-code a.qr-code-link {
    padding: 5px;
    width: 100px;
    margin: 0;
    font-size: 14px;
    text-align: center;}

.qr-images {
    display: flex;
    justify-content: center;}

.qr-code img.qr-code-img {
    padding: 5px;
    width: 110px;
    height: auto;}
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
    <p>Станция посадки: ' . $array_param_start[0] . '</p>
    <p style="margin: -10px 0 0 0;font-size: 12px;">' . $array_param_start[1] . '</p>
    ';
} else {
    $start_station = '
    <p>Станция посадки: ' . $array_param_start[0] . '</p>
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

$information_ticket = '';
if ($bus->information_ticket) {
    $information_ticket = '
        <p>' . $bus->information_ticket . '</p>
    ';
} else {
    $information_ticket = '
        <p>По предъявлению билета взять бесплатные бирки на багаж.</p>
    ';
}

$qrcode = '';
if($bus->qrcode) {
    $imgUrl = $config->httpHost . $bus->qrcode->url;
    $imgUrl = 'http://' . $imgUrl;
    $qrcode = '
    <div style="page-break-before: always;"></div>
    <div style="text-align: center;">
        <p style="text-align: center;">QR код для предъявления на вокзале</p>
        <div class="qr-images" style="text-align: center;">
            <img class="qr-code-img" src="' . $imgUrl . '" alt="" style="display: inline-block;">
        </div>
    </div>
    ';
}

$content .= '
' . $header_ticket . '

<h2 style="margin: 50px 0 20px 0;">Билет №' . $ticket->id . ' от ' . $ticket_date . '</h2>

<p>Перевозчик: ' . $transporter . '</p>
<p>Статус билета: ' . $ticket->pay_or_booking . '</p>
<p>Цена билета: ' . $ticket->price_ticket . ' руб.</p>

<p class="pdf-big">О РЕЙСЕ:</p>
<p>Место № ' . $ticket->seat . '</p>
' . $start_station . '
' . $finish_station . '

<p class="pdf-big">О ПАССАЖИРЕ:</p>
<p>Ф.И.О.: ' . $passenger->name_passenger . '</p>
<p>Вид документа: ' . $passenger->type_doc_passenger . '</p>
<p>Номер документа: ' . $passenger->passport_passenger . ' '. $passenger->num_doc_passenger .'</p>
<p>Дата рождения: ' . $passenger->birthday_passenger . '</p>
<p>Телефон: ' . $passenger->phone_passenger . '</p>

<div style="page-break-inside: avoid;">
    <p class="pdf-big">ДОПОЛНИТЕЛЬНАЯ ИНФОРМАЦИЯ:</p>

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td class="smalltext" style="width: 70%; vertical-align: top; font-size: 14px; padding-right: 15px; line-height: 1.4;">
                ' . $information_ticket . '
                <p>Компания Олимп осуществляет рейсы по следующим маршрутам:</p>
                <p>Москва – Таганрог</p>
                <p>Москва – Мариуполь</p>
                <p>Москва – Луганск</p>
                <p>Москва - Алчевск</p>
                <p>Москва – Стаханов</p>

                <p><strong>Заказ билетов не выходя из дома</strong></p>
                <p>+7 (926) 947-55-55</p>
                <p>+7 (959) 276-48-12</p>
                <p>+7 (916) 021-30-05</p>

                <p>Пришлем электронный билет вам на телефон (Вотсап, Телеграм).</p>
                <p>Оплата в автобусе наличными водителю или на сайте olimp-tickets.ru</p>
            </td>

            <td style="width: 30%; vertical-align: top; text-align: center; font-size: 12px;">
                <p style="margin: 0 0 8px 0; text-align: center; line-height: 1.3;">
                    <strong>Скидка 20% на заказ через приложение</strong>
                </p>

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
                    <tr>
                        <td style="width: 50%; text-align: center; font-size: 12px;">
                            <a href="https://apps.apple.com/ru/app/olimp-tickets/id6740780087?l=en-GB" style="color: #0000EE; text-decoration: underline;">IOS</a>
                        </td>
                        <td style="width: 50%; text-align: center; font-size: 12px;">
                            <a href="https://play.google.com/store/apps/details?id=com.mycompany.olimptickets" style="color: #0000EE; text-decoration: underline;">Android</a>
                        </td>
                    </tr>
                </table>

                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%; text-align: center;">
                            <img src="https://lk.olimp-tickets.ru/site/assets/images/qr-code1.png" alt="" style="width: 80px; height: 80px;">
                        </td>
                        <td style="width: 50%; text-align: center;">
                            <img src="https://lk.olimp-tickets.ru/site/assets/images/qr-code2.png" alt="" style="width: 80px; height: 80px;">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

' . $qrcode . '
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