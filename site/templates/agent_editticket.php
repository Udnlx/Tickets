<?php namespace ProcessWire;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

$selected_bus = !empty($_POST['selected_bus'])?$_POST['selected_bus']:NULL;  
$selected_id_bus = !empty($_POST['selected_id_bus'])?$_POST['selected_id_bus']:NULL;
$selected_date = !empty($_POST['selected_date'])?$_POST['selected_date']:NULL;
$selected_time = !empty($_POST['selected_time'])?$_POST['selected_time']:NULL;

$selected_seat = !empty($_POST['selected_seat'])?$_POST['selected_seat']:NULL;
$id_seat = !empty($_POST['id_seat'])?$_POST['id_seat']:NULL;
$old_pay_or_booking = !empty($_POST['old_pay_or_booking'])?$_POST['old_pay_or_booking']:NULL;
$old_booking_sum = !empty($_POST['old_booking_sum'])?$_POST['old_booking_sum']:NULL;
$old_confirm = !empty($_POST['old_confirm'])?$_POST['old_confirm']:NULL;
$old_agent_ticket = !empty($_POST['old_agent_ticket'])?$_POST['old_agent_ticket']:NULL;
$old_price_ticket = !empty($_POST['old_price_ticket'])?$_POST['old_price_ticket']:NULL;
$passenger = !empty($_POST['passenger'])?$_POST['passenger']:NULL;

$pay_or_booking = !empty($_POST['pay_or_booking'])?$_POST['pay_or_booking']:NULL;
$booking_sum = !empty($_POST['booking_sum'])?$_POST['booking_sum']:NULL;
$confirm = !empty($_POST['confirm'])?$_POST['confirm']:NULL;
$agent_ticket = !empty($_POST['agent_ticket'])?$_POST['agent_ticket']:NULL;
$price_ticket = !empty($_POST['price_ticket'])?$_POST['price_ticket']:NULL;

$success = 'Статус билета успешно изменен';
$log = '';
if ($selected_bus && $selected_id_bus && $selected_date && $selected_time && $selected_seat && $id_seat && $old_pay_or_booking && $old_confirm && $old_agent_ticket && $passenger && $pay_or_booking && $confirm && $agent_ticket && $price_ticket) {
    $log .= date('Y-m-d H:i:s') . ' - Изменен билет id - ' . $id_seat . '. ';
    $log .= 'Статус изменен с ' . $old_pay_or_booking . ' на ' . $pay_or_booking . ' оператором ' . $operator . '. '; 
    $log .= 'Сумма к оплате при бронировании изменена с ' . $old_booking_sum . ' на ' . $booking_sum . ' оператором ' . $operator . '. ';
    $log .= 'Статус подтверждения изменен с ' . $old_confirm . ' на ' . $confirm . ' оператором ' . $operator . '. ';  
    $log .= 'Агент изменен с ' . $old_agent_ticket . ' на ' . $agent_ticket . ' оператором ' . $operator . '. ';
    $log .= 'Цена билета изменена с ' . $old_price_ticket . ' на ' . $price_ticket . ' оператором ' . $operator . '. ';
    $log .= 'Параметры измененного билета: ' . $selected_bus . ' ' . $selected_date . '' . $selected_time . ', id автобуса - ' . $selected_id_bus . ', место - ' . $selected_seat . ', пассажир - ' . $passenger; 
    file_put_contents(__DIR__ . '/log_agent_edit_tikets.txt', $log . PHP_EOL, FILE_APPEND);
    
    $edit_page = $pages->get('template=purchased_tickets, id=' . $id_seat . '');
    $edit_page->of(false);
    $edit_page->pay_or_booking = $pay_or_booking;
    $edit_page->booking_sum = $booking_sum;
    $edit_page->confirm = $confirm;
    $edit_page->agent_ticket = $agent_ticket;
    $edit_page->price_ticket = $price_ticket;
    $edit_page->save();
} else {
    $success = 'Статус билета не изменен!<br>Ошибка в данных';
}



if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Статус билета не изменен</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center"><?php echo $success; ?></h1>
	
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
        <h4 class="uk-margin-remove">Данные о билете:</h4>
        <p class="uk-margin-remove">Автобус: <span style="font-weight: 700;"><?php echo $selected_bus; ?></span></p>
        <p class="uk-margin-remove">ID автобуса: <span style="font-weight: 700;"><?php echo $selected_id_bus; ?></span></p>
        <p class="uk-margin-remove">Дата и время отправления: <span style="font-weight: 700;"><?php echo $selected_date; ?> <?php echo $selected_time; ?></span></p>
        <p class="uk-margin-remove">Место: <span style="font-weight: 700;"><?php echo $selected_seat; ?></span></p>
        <p class="uk-margin-remove">ID билета: <span style="font-weight: 700;"><?php echo $id_seat; ?></span></p>
        <p class="uk-margin-remove">Пассажир: <span style="font-weight: 700;"><?php echo $passenger; ?></span></p>
        <p class="uk-margin-remove">Старый статус: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $old_pay_or_booking; ?></span></p>
        <p class="uk-margin-remove">Новый статус: <span class="uk-text-success" style="font-weight: 700;"><?php echo $pay_or_booking; ?></span></p>
        <p class="uk-margin-remove">Старая сумма при бронировании: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $old_booking_sum; ?></span></p>
        <p class="uk-margin-remove">Новая сумма при бронировании: <span class="uk-text-success" style="font-weight: 700;"><?php echo $booking_sum; ?></span></p>
        <p class="uk-margin-remove">Старый статус подтверждения: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $old_confirm; ?></span></p>
        <p class="uk-margin-remove">Новый статус подтверждения: <span class="uk-text-success" style="font-weight: 700;"><?php echo $confirm; ?></span></p>
        <p class="uk-margin-remove">Старый агент: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $old_agent_ticket; ?></span></p>
        <p class="uk-margin-remove">Новый агент: <span class="uk-text-success" style="font-weight: 700;"><?php echo $agent_ticket; ?></span></p>

        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>
	
<?php   
}
?>