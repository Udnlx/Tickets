<?php namespace ProcessWire;

$agent = !empty($_POST['agent'])?$_POST['agent']:NULL;  
$start_date = !empty($_POST['start_date'])?$_POST['start_date']:NULL;
$finish_date = !empty($_POST['finish_date'])?$_POST['finish_date']:NULL;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Печать отчета по агенту</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php
$start = strtotime( date($start_date) . " 00:00:00");
$end = strtotime( date($finish_date) . " 23:59:59");
$all_agent_tickets = $pages->find('template=purchased_tickets, published>' . $start . ', published<' . $end . ', agent_ticket=' . $agent . ', sort=date_depart, sort=bus');
$sum_predoplata = 0;
$sum_ostatok = 0;
$arr_all_agent_tickets = [];
foreach ($all_agent_tickets as $all_agent_tickets_item) {
    // $arr_all_agent_tickets[] = array(
    //     "agent"=>$all_agent_tickets_item->agent_ticket,
    //     "bus"=>$all_agent_tickets_item->title,
    //     "pay_or_booking"=>$all_agent_tickets_item->pay_or_booking,
    //     "confirm"=>$all_agent_tickets_item->confirm,
    //     "price_ticket"=>$all_agent_tickets_item->price_ticket,
    //     "booking_sum"=>$all_agent_tickets_item->booking_sum,
    //     "passenger"=>$all_agent_tickets_item->passenger,
    //     "type_ticket"=>$all_agent_tickets_item->type_ticket,
    //     "passenger_doc"=>$all_agent_tickets_item->passenger_doc,
    //     "operator"=>$all_agent_tickets_item->operator,
    //     "reg_ticket"=>date("Y-m-d H:i:s", $all_agent_tickets_item->published) 
    //     );

    $commission = 0;
    if ($all_agent_tickets_item->id_bus == 1019 || $all_agent_tickets_item->id_bus == 1022) {
        $commission = 550;
    } else {
        $commission = 650;
    }

    if ($all_agent_tickets_item->booking_sum > 0) {
        $sum_predoplata = $sum_predoplata + $all_agent_tickets_item->booking_sum;
    }

    $remains = 0;
    if ($all_agent_tickets_item->booking_sum > 0) {
        $remains = $commission - $all_agent_tickets_item->booking_sum;
        $sum_ostatok = $sum_ostatok + $remains;
    } else {
        $remains = $commission;
        $sum_ostatok = $sum_ostatok + $remains;
    }

    $confirm = '';
    if ($all_agent_tickets_item->confirm == 'подтверждено') {
        $confirm = 'подтверждено';
    }

    $arr_all_agent_tickets[] = array(
        "date"=>$all_agent_tickets_item->date_depart,
        "passenger"=>$all_agent_tickets_item->passenger,
        "type_ticket"=>$all_agent_tickets_item->type_ticket,
        "bus"=>$all_agent_tickets_item->bus,
        "commission"=>$commission,
        "booking_sum"=>$all_agent_tickets_item->booking_sum,
        "remains"=>$remains,
        "confirm"=>$confirm
        );
}
//echo '<pre>'; print_r($arr_all_agent_tickets); echo '</pre>';

$title = array
(
'Отчет по агенту ' . $agent . ' - ' . $start_date . ' - ' . $finish_date,
'',
);

$headers = array(
	// array(
	// 	'agent' => 'Агент',
	// 	'bus' => 'Автобус',
	// 	'pay_or_booking' => 'Куплен/Забронирован',
	// 	'confirm' => 'Статус подтверждения',
	// 	'price_ticket' => 'Стоимость билета',
	// 	'booking_sum' => 'Сумма к оплате',
	// 	'passenger' => 'Пассажир',
	// 	'type_ticket' => 'Тип билета',
	// 	'passenger_doc' => 'Документ',
	// 	'operator' => 'Оператор',
	// 	'reg_ticket' => 'Регистрация билета',
	// ),  

    array(
        'date' => 'Дата',
        'passenger' => 'Пассажир',
        'type_ticket' => 'Тип билета',
        'bus' => 'Автобус',
        'commission' => 'Комиссия',
        'booking_sum' => 'Предоплата',
        'remains' => 'Остаток к расчету',
        'confirm' => 'Статус подтверждения'
    ),   
);

$footer = array(
    array(
        'date' => 'ИТОГО',
        'passenger' => '',
        'type_ticket' => '',
        'bus' => '',
        'commission' => '',
        'booking_sum' => $sum_predoplata,
        'remains' => $sum_ostatok,
        'confirm' => ''
    ),   
);

header('Content-Type: text/csv; charset=utf-8' );
header(sprintf( 'Content-Disposition: attachment; filename=Отчет по агенту ' . $agent . ' - %s.csv', date( 'dmY-His' ) ) );
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
foreach($arr_all_agent_tickets as $val) { 
    $val = mb_convert_encoding($val, 'windows-1251', 'utf-8');
	fputcsv($buffer, $val, ';'); 
} 
foreach($footer as $val) { 
    $val = mb_convert_encoding($val, 'windows-1251', 'utf-8');
    fputcsv($buffer, $val, ';'); 
} 
fclose($buffer); 
exit();
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Печать отчета по агенту</h1>
	
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
        <h4 class="uk-margin-remove">Выбранный агент: <span style="font-weight: 700;"><?php echo $agent; ?></span></h4>
        <h4 class="uk-margin-remove">Дата: с <span style="font-weight: 700;"><?php echo $start_date; ?></span> по <span style="font-weight: 700;"><?php echo $finish_date; ?></span></h4>
        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>





<?php   
}
?>