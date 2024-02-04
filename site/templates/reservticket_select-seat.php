<?php namespace ProcessWire;

$selected_bus = !empty($_POST['post_bus'])?$_POST['post_bus']:NULL;  
$selected_id_bus = !empty($_POST['post_id_bus'])?$_POST['post_id_bus']:NULL;
$selected_date = !empty($_POST['post_date'])?$_POST['post_date']:NULL;
$selected_time = !empty($_POST['post_time'])?$_POST['post_time']:NULL;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Выбор мест для брони билетов</h1>
	
	            
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
$arr_reserv_seat = [];
foreach ($reserv_seat as $reserv_seat_item) {
    $arr_reserv_seat[] = array(
        "seat"=>$reserv_seat_item->seat,
        "pay_or_booking"=>$reserv_seat_item->pay_or_booking,
        "confirm"=>$reserv_seat_item->confirm,
        "station"=>$reserv_seat_item->name_station,
        "id_passenger"=>$reserv_seat_item->id_passenger,
        "passenger"=>$reserv_seat_item->passenger,
        "type_ticket"=>$reserv_seat_item->type_ticket,
        "passenger_doc"=>$reserv_seat_item->passenger_doc,
        "operator"=>$reserv_seat_item->operator
        );
}
//echo '<pre>'; print_r($arr_reserv_seat); echo '</pre>';

$max_seat = 53;
$button_seat = '';
for ($num_seat = 1; $num_seat <= $max_seat; $num_seat++) {
    $free = true;
    foreach ($arr_reserv_seat as $key => $val) {
        $data_passenger = $pages->get('template=passengers, id=' . $val['id_passenger'] . '');
        $phone_passenger = $data_passenger->phone_passenger;
        if ($val['seat'] == $num_seat) {
                $free = false;
                $conf_status = '';
                if ($val['confirm'] == 'не подтверждено') {
                    $conf_status = '<p class="noconfirm"><i class="fa-solid fa-phone"></i></p>';
                } else {
                    $conf_status = '<p class="confirm"><i class="fa-solid fa-phone"></i></p>';
                }
                if ($val['pay_or_booking'] == 'забронировано') {
                    $button_seat .= '
                    <button class="uk-mass-reserv-seat uk-margin-small-top uk-button uk-button-default seat_reserv" disabled title="Место забронировано: ' . $val['passenger'] . ', телефон: ' . $phone_passenger . ', станция посадки: ' . $val['station'] . '">' . $val['seat'] . '' . $conf_status . '</button>
                    ';
                }
                if ($val['pay_or_booking'] == 'оплачено') {
                    $button_seat .= '
                    <button class="uk-mass-reserv-seat uk-margin-small-top uk-button uk-button-default seat_pay" disabled title="Место оплачено: ' . $val['passenger'] . ', телефон: ' . $phone_passenger . ', станция посадки: ' . $val['station'] . '">' . $val['seat'] . '' . $conf_status . '</button>
                    ';
                }
        }
    }
    if ($free == true) {
        if ($num_seat < 10) {
            $num_seat = '0' . $num_seat;
        }
        $button_seat .= '
        <button class="uk-mass-reserv-seat uk-margin-small-top uk-button uk-button-default seat_free">' . $num_seat . '</button>
        ';
    }
}
?>

<div id="content">
	<h1 class="uk-heading-hero uk-text-center">Выбор мест для брони билетов</h1>
	<div class="uk-child-width-1-2@m" uk-grid>
	    
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" uk-sticky>
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <h4 class="uk-margin-remove">Выбранный рейс:<br><span style="font-weight: 700;"><?php echo $selected_bus; ?></span></h4>
                <h4 class="uk-margin-remove">Дата: <span style="font-weight: 700;"><?php echo $selected_date; ?></span> отправление<span style="font-weight: 700;"><?php echo $selected_time; ?></span></h4>
                <form class="uk-flex uk-flex-column" id="select_seat" action="/registratciia-bileta/" method="post">
                    <div class="uk-margin-small-top">
                        <input class="uk-input" id="selected_bus" type="text" name="selected_bus" value="<?php echo $selected_bus; ?>">
                    </div>
                    <div class="uk-margin-small-top">
                        <input class="uk-input" id="selected_id_bus" type="text" name="selected_id_bus" value="<?php echo $selected_id_bus; ?>">
                    </div>
                    <div class="uk-margin-small-top">
                        <input class="uk-input" id="selected_date" type="text" name="selected_date" value="<?php echo $selected_date; ?>">
                    </div>
                    <div class="uk-margin-small-top">
                        <input class="uk-input" id="selected_time" type="text" name="selected_time" value="<?php echo $selected_time; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="select_reserv_seat" type="text" name="select_reserv_seat" value="" autocomplete="off" required>
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button id="reserv_seat-btn" class="uk-margin-small-top uk-button uk-button-default" type="submit">Забронировать</button>
                        <a class="uk-margin-small uk-button uk-button-default" href="/registratciia-bileta-vybor-reisa/">К выбору рейса</a>
                    </div>
                    <div id="seat_messages" class="messages-block">
                        <p class="messages" style="color: green;"></p>
                    </div>
                </form>
            </div>
        </div>
        
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Свободные и занятые места</h3>
                <div class="buttons_seat uk-flex uk-flex-wrap">
                    <?php echo $button_seat; ?>
                </div>
            </div>
        </div>
        
    </div>
</div>





<?php   
}
?>