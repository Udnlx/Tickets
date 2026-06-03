<?php namespace ProcessWire;

$selected_bus = !empty($_POST['print_bus'])?$_POST['print_bus']:NULL;  
$selected_id_bus = !empty($_POST['print_id_bus'])?$_POST['print_id_bus']:NULL;
$selected_time = !empty($_POST['print_time'])?$_POST['print_time']:NULL;

$agent = !empty($_POST['print_agent'])?$_POST['print_agent']:NULL;
$print_agent = !empty($_POST['print_agent'])?$_POST['print_agent']:NULL;
if ($agent == 'Олимп|Site|APP') {
    $agent = 'Олимп|Site|APP';
    $print_agent = 'Олимп + API';
}

$start_date = !empty($_POST['print_start_date'])?$_POST['print_start_date']:NULL;
$finish_date = !empty($_POST['print_finish_date'])?$_POST['print_finish_date']:NULL;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Печать отчета по рейсу и агенту за период</h1>
	<h3 class="uk-margin-remove uk-card-title uk-text-center">По дате выезда</h3> 
    <br>
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php
$all_agent_tickets = $pages->find('template=purchased_tickets, id_bus=' . $selected_id_bus . ', date_depart>=' . $start_date . ', date_depart<=' . $finish_date . ', agent_ticket=' . $agent . ', sort=date_depart');
$sum_price_ticket = 0;
$sum_commission = 0;
$sum_predoplata = 0;
$sum_ostatok = 0;
$arr_all_agent_tickets = [];
foreach ($all_agent_tickets as $all_agent_tickets_item) {
	// $commission = 0;
    // if ($all_agent_tickets_item->id_bus == 1019 || $all_agent_tickets_item->id_bus == 1022) {
    //     if ($all_agent_tickets_item->confirm == 'не явился') {
    //         $commission = 0;
    //     } else {
    //         $commission = 550;
    //     }
    //     $sum_commission = $sum_commission + $commission;
    //     $sum_price_ticket = $sum_price_ticket + $all_agent_tickets_item->price_ticket;
    // } else {
    //     if ($all_agent_tickets_item->confirm == 'не явился') {
    //         $commission = 0;
    //     } else {
    //         $commission = 650;
    //     }
    //     $sum_commission = $sum_commission + $commission;
    //     $sum_price_ticket = $sum_price_ticket + $all_agent_tickets_item->price_ticket;
    // }

    // $commission = 0;
    // if ($all_agent_tickets_item->id_bus == 1019 || $all_agent_tickets_item->id_bus == 1022) {
    //     if ($all_agent_tickets_item->confirm == 'не явился') {
    //         $commission = 0;
    //     } else {
    //         $commission = 550;
    //     }
    //     $sum_commission = $sum_commission + $commission;
    //     $sum_price_ticket = $sum_price_ticket + $all_agent_tickets_item->price_ticket;
    // } elseif ($all_agent_tickets_item->id_bus == 73710 || $all_agent_tickets_item->id_bus == 73723) {
    //     if ($all_agent_tickets_item->agent_ticket == 'Олимп' || $all_agent_tickets_item->agent_ticket == 'Котельники' || $all_agent_tickets_item->agent_ticket == 'Site' || $all_agent_tickets_item->agent_ticket == 'APP') {
    //         if ($all_agent_tickets_item->confirm == 'не явился') {
    //             $commission = 0;
    //         } else {
    //             $commission = 500;
    //         }
    //         $sum_commission = $sum_commission + $commission;
    //         $sum_price_ticket = $sum_price_ticket + $all_agent_tickets_item->price_ticket;
    //     } else {
    //         if ($all_agent_tickets_item->confirm == 'не явился') {
    //             $commission = 0;
    //         } else {
    //             $commission = 650;
    //         }
    //         $sum_commission = $sum_commission + $commission;
    //         $sum_price_ticket = $sum_price_ticket + $all_agent_tickets_item->price_ticket;
    //     }
    // } else {
    //     if ($all_agent_tickets_item->confirm == 'не явился') {
    //         $commission = 0;
    //     } else {
    //         $commission = 650;
    //     }
    //     $sum_commission = $sum_commission + $commission;
    //     $sum_price_ticket = $sum_price_ticket + $all_agent_tickets_item->price_ticket;
    // }

    // $commission = 0;
    // if ($all_agent_tickets_item->confirm !== 'не явился') {
    //     if ($all_agent_tickets_item->agent_ticket === 'ИП Слабоспицкий') {
    //         $commission = 500;
    //     } elseif ($all_agent_tickets_item->id_bus == 1019 || $all_agent_tickets_item->id_bus == 1022) {
    //         $commission = 550;
    //     } elseif ($all_agent_tickets_item->id_bus == 73710 || $all_agent_tickets_item->id_bus == 73723) {
    //         if ($all_agent_tickets_item->agent_ticket === 'Олимп' || $all_agent_tickets_item->agent_ticket === 'Котельники') {
    //             $commission = 500;
    //         } else {
    //             $commission = 650;
    //         }
    //     } else {
    //         $commission = 650;
    //     }
    // }
    // $sum_commission += $commission;

    $commission = 0;
    if ($all_agent_tickets_item->confirm !== 'не явился') {
        if ($all_agent_tickets_item->agent_ticket === 'ИП Слабоспицкий') {
            $commission = 500;
        } elseif ($all_agent_tickets_item->id_bus == 1019 || $all_agent_tickets_item->id_bus == 1022) {
            $commission = 500;
        } elseif ($all_agent_tickets_item->id_bus == 73710 || $all_agent_tickets_item->id_bus == 73723) {
            if ($all_agent_tickets_item->agent_ticket === 'Олимп' || $all_agent_tickets_item->agent_ticket === 'Котельники') {
                $commission = 500;
            } else {
                $commission = 500;
            }
        } else {
            $commission = 500;
        }
    }
    $sum_commission += $commission;

    $remains = 0;
    if ($all_agent_tickets_item->booking_sum > 0) {
        $remains = $all_agent_tickets_item->price_ticket - $commission - $all_agent_tickets_item->booking_sum;
        $sum_predoplata = $sum_predoplata + $all_agent_tickets_item->booking_sum;
        $sum_ostatok = $sum_ostatok + $remains;
    } else {
        $remains = $all_agent_tickets_item->price_ticket - $commission;
        $sum_ostatok = $sum_ostatok + $remains;
    }

    $confirm = '';
    if ($all_agent_tickets_item->confirm == 'не явился') {
        $confirm = 'не явился';
    }

    $price_ticket = (int)$all_agent_tickets_item->price_ticket;

    $arr_all_agent_tickets[] = array(
        "date"=>$all_agent_tickets_item->date_depart,
        "passenger"=>$all_agent_tickets_item->passenger,
        "type_ticket"=>$all_agent_tickets_item->type_ticket,
        "bus"=>$all_agent_tickets_item->bus,
        "price_ticket"=>$price_ticket,
        "commission"=>$commission,
        "booking_sum"=>$all_agent_tickets_item->booking_sum,
        "remains"=>$remains,
        "confirm"=>$confirm,
        "reg_ticket"=>date("Y-m-d H:i:s", $all_agent_tickets_item->published),
        "comment"=>$all_agent_tickets_item->comment,
        );
    }
	//echo '<pre>'; print_r($arr_reserv_seat); echo '</pre>';
	$arr_all_agent_tickets[] = array(
        'date' => 'ИТОГО',
        'passenger' => '',
        'type_ticket' => '',
        'bus' => '',
        "price_ticket"=>$sum_price_ticket,
        'commission' => $sum_commission,
        'booking_sum' => $sum_predoplata,
        'remains' => $sum_ostatok,
        'confirm' => ''
            );
    $arr_all_agent_tickets[] = array(
        'date' => '',
        'comment' => '',
            );

$title = array
(
'Отчет по маршруту ' . $selected_bus . ' агент: ' . $print_agent . ' за период с ' . $start_date . ' по ' . $finish_date,
'',
);

$headers = array(
	array(
		'date' => 'Дата',
        'passenger' => 'Пассажир',
        'type_ticket' => 'Тип билета',
        'bus' => 'Автобус',
        'price_ticket' => 'Цена билета',
        'commission' => 'Комиссия',
        'booking_sum' => 'Предоплата',
        'remains' => 'Остаток к расчету',
        'confirm' => 'Статус подтверждения',
        'reg_ticket' => 'Регистрация билета',
        'comment' => 'Комментарий'
	),    
);

$footer = array(
    array(
        'date' => '',
        'passenger' => '',
        'type_ticket' => '',
        'bus' => '',
        'price_ticket' => '',
        'commission' => '',
        'booking_sum' => '',
        'remains' => '',
        'confirm' => '',
        'reg_ticket' => '',
        'comment' => '',
    ),   
);

header('Content-Type: text/csv; charset=utf-8' );
header(sprintf( 'Content-Disposition: attachment; filename=Отчет по маршруту ' . $selected_bus . ' и агенту ' . $print_agent . ' - %s.csv', date( 'dmY-His' ) ) );
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
	<h1 class="uk-heading-hero uk-text-center">Печать отчета по рейсу и агенту за период</h1>
    <h3 class="uk-margin-remove uk-card-title uk-text-center">По дате выезда</h3> 
    <br>
	
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
        <h4 class="uk-margin-remove">Выбранный рейс:<br><span style="font-weight: 700;"><?php echo $selected_bus; ?></span></h4>
        <h4 class="uk-margin-remove">Выбранный агент:<br><span style="font-weight: 700;"><?php echo $print_agent; ?></span></h4>
        <h4 class="uk-margin-remove">Дата: с <span style="font-weight: 700;"><?php echo $start_date; ?></span> по <span style="font-weight: 700;"><?php echo $finish_date; ?></span></h4>
        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>





<?php   
}
?>