<?php namespace ProcessWire;

$access = '';
if(isset($_SESSION['access'])){
    $access = $_SESSION['access'];
}

$selected_bus = !empty($_POST['post_bus'])?$_POST['post_bus']:NULL;  
$selected_id_bus = !empty($_POST['post_id_bus'])?$_POST['post_id_bus']:NULL;
$selected_date = !empty($_POST['post_date'])?$_POST['post_date']:NULL;
$selected_time = !empty($_POST['post_time'])?$_POST['post_time']:NULL;

$bus_page = $pages->get('id=' . $selected_id_bus . '');

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator' || $access == 'agent') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Выбор мест для резерва билетов</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php
$mass_reserv_seats_page = $pages->get('template=reserv_seats, id_bus=' . $selected_id_bus . ', date_depart=' . $selected_date . '');
//echo $mass_reserv_seats_page->mass_reserv_seats;
if ($mass_reserv_seats_page->id > 0) {
    $arr_mass_reserv_seat_agent = explode(',', $mass_reserv_seats_page->mass_reserv_seats_agent);
    $arr_mass_reserv_special_agent = explode(',', $mass_reserv_seats_page->mass_reserv_special_agent);
    $arr_mass_reserv_seat = explode(',', $mass_reserv_seats_page->mass_reserv_seats);
} else {
    $arr_mass_reserv_seat_agent = [0];
    $arr_mass_reserv_special_agent = [0];
    $arr_mass_reserv_seat = [0];
}
//echo '<pre>'; print_r($arr_mass_reserv_seat); echo '</pre>';

$reserv_seat = $pages->find('template=purchased_tickets, id_bus=' . $selected_id_bus . ', date_depart=' . $selected_date . ',sort=seat');
$arr_reserv_seat = [];
foreach ($reserv_seat as $reserv_seat_item) {
    $arr_reserv_seat[] = array(
        "seat"=>$reserv_seat_item->seat,
        "pay_or_booking"=>$reserv_seat_item->pay_or_booking,
        "confirm"=>$reserv_seat_item->confirm,
        "station"=>$reserv_seat_item->name_station,
        "station_finish"=>$reserv_seat_item->name_station_finish,
        "id_passenger"=>$reserv_seat_item->id_passenger,
        "passenger"=>$reserv_seat_item->passenger,
        "type_ticket"=>$reserv_seat_item->type_ticket,
        "passenger_doc"=>$reserv_seat_item->passenger_doc,
        "operator"=>$reserv_seat_item->operator,
        "agent_ticket"=>$reserv_seat_item->agent_ticket,
        "comment"=>$reserv_seat_item->comment,
        "sb_ticket_id"=>$reserv_seat_item->sb_ticket_id
        );
}
//echo '<pre>'; print_r($arr_reserv_seat); echo '</pre>';

$reestr_seat_1c = '';
$sb_dispatch_place_id = $bus_page->sb_dispatch_place_id;
$sb_arrival_place_id = $bus_page->sb_arrival_place_id;
$sb_dispatch_date = $selected_date;
$sb_dispatch_time = $bus_page->sb_dispatch_time;
include 'sb_get_freeseat.php';
$reestr_seat_1c = '
<div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
<ul uk-accordion>
    <li>
        <a class="uk-accordion-title" href="#"><h3 class="uk-margin-remove uk-card-title">Лог работы с системой 1С</h3></a>
        <div class="uk-accordion-content">
            ' . $sb_log . '
        </div>
    </li>
</ul>
</div>
';
if ($switching_on == true) {
    $switch_sb_connect = '<p class="sb_switch_item">🔘🟢 Подключение к 1С</p>';
}
if ($switching_on == false) {
    $switch_sb_connect = '<p class="sb_switch_item">🔴🔘 Подключение к 1С</p>';
}
if ($bus_on == true) {
    $switch_sb_bus = '<p class="sb_switch_item">🔘🟢 В 1С найден автобус</p>';
}
if ($bus_on == false) {
    $switch_sb_bus = '<p class="sb_switch_item">🔴🔘 В 1С не найден автобус</p>';
}
// echo '<pre>'; 
// var_dump($sb_free_seats);
// echo '</pre>';

$max_seat = 53;
$button_seat = '';
$sb_title_error = '';
for ($num_seat = 1; $num_seat <= $max_seat; $num_seat++) {
    $sb_disabled = '';
    $sb_occupied = '';
    $sb_on = '';
    if (in_array($num_seat, $sb_free_seats)) {
        $sb_occupied = '<p class="sb_occupied"></p>'; 
    }
    if (in_array($num_seat, $sb_occupied_seats)) {
        $sb_disabled = 'disabled';
        $sb_occupied = '<p class="sb-marker">1С</p>'; 
        $sb_on = 'on';
    }
    $free = true;
    foreach ($arr_reserv_seat as $key => $val) {
        $data_passenger = $pages->get('template=passengers, id=' . $val['id_passenger'] . '');
        $phone_passenger = $data_passenger->phone_passenger;
        $sb_error = '';
        if ($sb_on == 'on' && !$val['sb_ticket_id']) {
            $sb_error = '<p class="sb_error"></p>';
            $sb_title_error = '
            <p class="uk-margin-remove" style="color: red; font-weight: 700; line-height: 1;">Внимание, расхождение данных! На рейсе имеются места, которые в 1С уже заняты, но в системе по 1С не проведены</p>
            ';
        }
        if ($val['seat'] == $num_seat) {
                $free = false;
                $conf_status = '';
                if ($val['confirm'] == 'не подтверждено' || $val['confirm'] == 'подтверждено' || $val['confirm'] == 'явился') {
                    $conf_status = '<p class="appeared"></p>';
                } else {
                    $conf_status = '<p class="noappeared"><i class="fa-solid fa-triangle-exclamation"></i></p>';
                }
                if ($val['agent_ticket'] == 'TestAPI' || $val['agent_ticket'] == 'Site' || $val['agent_ticket'] == 'APP') {
                    $conf_status = '<p class="noappeared">API</p>';
                }

                if ($val['pay_or_booking'] == 'забронировано') {
                    $button_seat .= '
                    <button class="uk-mass-reserv-seat uk-margin-small-top uk-button uk-button-default seat_reserv" disabled title="Место забронировано: ' . $val['passenger'] . ', телефон: ' . $phone_passenger . ', станция посадки: ' . $val['station'] . ', станция высадки: ' . $val['station_finish'] . '">' . $val['seat'] . '' . $conf_status . '' . $sb_occupied . '' . $sb_error . '</button>
                    ';
                }
                if ($val['pay_or_booking'] == 'оплачено') {
                    $button_seat .= '
                    <button class="uk-mass-reserv-seat uk-margin-small-top uk-button uk-button-default seat_pay" disabled title="Место оплачено: ' . $val['passenger'] . ', телефон: ' . $phone_passenger . ', станция посадки: ' . $val['station'] . ', станция высадки: ' . $val['station_finish'] . '">' . $val['seat'] . '' . $conf_status . '' . $sb_occupied . '' . $sb_error . '</button>
                    ';
                }
        }
    }
    if ($free == true) {
        if ($num_seat < 10) {
            $num_seat = '0' . $num_seat;
        }
        $reserv_style = '';
        foreach ($arr_mass_reserv_seat_agent as $itm) {
            if ($num_seat == $itm) {
                $reserv_style = 'seat_select_mass_agent';
            }
        }
        foreach ($arr_mass_reserv_special_agent as $itm) {
            if ($num_seat == $itm) {
                $reserv_style = 'seat_select_special_agent';
            }
        }
        foreach ($arr_mass_reserv_seat as $itm) {
            if ($num_seat == $itm) {
                $reserv_style = 'seat_select_mass';
            }
        }
        // $button_seat .= '
        // <button class="uk-mass-reserv-seat uk-margin-small-top uk-button uk-button-default seat_free ' . $reserv_style . '">' . $num_seat . '</button>
        // ';
        if ($sb_disabled != 'disabled') {
            $button_seat .= '
            <button class="uk-mass-reserv-seat uk-margin-small-top uk-button uk-button-default seat_free ' . $reserv_style . '">' . $num_seat . '' . $sb_occupied . '</button>
            ';
        } else {
            $button_seat .= '
            <button class="uk-mass-reserv-seat uk-margin-small-top uk-button uk-button-default seat_free" disabled>' . $num_seat . '' . $sb_occupied . '</button>
            ';
        }
    }
}
?>

<div id="content">
	<h1 class="uk-heading-hero uk-text-center">Выбор мест для резерва билетов</h1>
	<div class="uk-child-width-1-2@m" uk-grid>
	    
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" uk-sticky>
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <h4 class="uk-margin-remove">Выбранный рейс:<br><span style="font-weight: 700;"><?php echo $selected_bus; ?></span></h4>
                <h4 class="uk-margin-remove">Дата: <span style="font-weight: 700;"><?php echo $selected_date; ?></span> отправление<span style="font-weight: 700;"><?php echo $selected_time; ?></span></h4>
                <div class="uk-margin-small-top uk-hidden">
                    <input class="uk-input" id="operator" type="text" name="operator" value="<?php echo $operator; ?>">
                </div>
                <div class="uk-margin-small-top uk-hidden">
                    <input class="uk-input" id="selected_bus" type="text" name="selected_bus" value="<?php echo $selected_bus; ?>">
                </div>
                <div class="uk-margin-small-top uk-hidden">
                    <input class="uk-input" id="selected_id_bus" type="text" name="selected_id_bus" value="<?php echo $selected_id_bus; ?>">
                </div>
                <div class="uk-margin-small-top uk-hidden">
                    <input class="uk-input" id="selected_date" type="text" name="selected_date" value="<?php echo $selected_date; ?>">
                </div>
                <div class="uk-margin-small-top uk-hidden">
                    <input class="uk-input" id="selected_time" type="text" name="selected_time" value="<?php echo $selected_time; ?>">
                </div>
                
                <div class="uk-margin-small-top uk-hidden">
                    <input class="uk-input readonly" id="select_reserv_seat" type="text" name="select_reserv_seat" value="" autocomplete="off" required>
                </div>
                <div class="uk-margin-small-top uk-hidden">
                    <input class="uk-input readonly" id="select_special_reserv_seat" type="text" name="select_special_reserv_seat" value="" autocomplete="off" required>
                </div>
                
                <div class="uk-margin-small-top uk-flex uk-flex-column">
                    <button id="reserv_seat-btn" class="uk-margin-small-top uk-button uk-button-default" type="submit">Зарезервировать</button>
                    <a class="uk-margin-small uk-button uk-button-default" href="/rezerv-biletov-vybor-reisa/">К выбору рейса</a>
                </div>
                <div id="seat_messages" class="messages-block">
                    <p class="messages" style="color: green;"></p>
                </div>
            </div>
        </div>
        
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <div class="sb_switсh">
                    <?php echo $switch_sb_connect;?>
                    <?php echo $switch_sb_bus;?>
                </div>
                <h3 class="uk-margin-remove uk-card-title">Выбор и отмена мест для резерва</h3>
                <?php echo $sb_title_error;?>
                <div class="buttons_seat uk-flex uk-flex-wrap">
                    <?php echo $button_seat; ?>
                </div>
            </div>
            <br>
            <?php echo $reestr_seat_1c;?>
        </div>
        
    </div>
</div>





<?php   
}
?>