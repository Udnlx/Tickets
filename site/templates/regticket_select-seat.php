<?php namespace ProcessWire;

$access = '';
if(isset($_SESSION['access'])){
    $access = $_SESSION['access'];
}

$selected_bus = !empty($_POST['post_bus'])?$_POST['post_bus']:NULL;  
$selected_id_bus = !empty($_POST['post_id_bus'])?$_POST['post_id_bus']:NULL;
$selected_date = !empty($_POST['post_date'])?$_POST['post_date']:NULL;
$selected_time = !empty($_POST['post_time'])?$_POST['post_time']:NULL;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator' || $access == 'agent') {
?>

<div id="content" style="max-width: 700px;">
    <h1 class="uk-heading-hero uk-text-center">Выбор места для регистрации билета</h1>
    
                
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php

$button_station_start = '';
$bus_page = $pages->get('id=' . $selected_id_bus . '');
foreach ($bus_page->station_start as $item) {
$array = preg_split('/[—]/u', $item->title, -1, PREG_SPLIT_NO_EMPTY);
//print_r($array);
$button_station_start .= '
<button id="' . $item->id . '" param_btn="' . $item->title . '" class="uk-ticket-button-station-start uk-margin-small-top uk-button uk-button-default">' . $array[0] . '</button>
';
}

$button_station_finish = '';
$bus_page = $pages->get('id=' . $selected_id_bus . '');
foreach ($bus_page->station_finish as $item) {
$array = preg_split('/[—]/u', $item->title, -1, PREG_SPLIT_NO_EMPTY);
//print_r($array);
$button_station_finish .= '
<button id="' . $item->id . '" param_btn="' . $item->title . '" class="uk-ticket-button-station-finish uk-margin-small-top uk-button uk-button-default">' . $array[0] . '</button>
';
}

$prices = '';
$bus_page = $pages->get('id=' . $selected_id_bus . '');
if (count($bus_page->table_price) > 0) {
    foreach ($bus_page->table_price as $item) {
    $prices .= '
    <p class="price-itm" ss="' . $item->name_station . '" idss="' . $item->sbid_station_start . '" sf="' . $item->name_station_finish . '" idsf="' . $item->sbid_station_finish . '" tp="' . $item->price_ticket . '">
        ' . $item->name_station . ' - ' . $item->name_station_finish . ' - ' . $item->price_ticket . '<br>
        ' . $item->sbid_station_start . ' - ' . $item->sbid_station_finish . '
    </p>
    ';
    }
} else {
    $prices = '
    <p class="price-itm" ss="" sf="" tp="">
        Таблицы цен у этого рейса нет
    </p>
    ';
}

$arr_extra_calendar = [];
$calendar_extra_price = 0;
if (count($bus_page->extra_calendar) > 0) {
    $year = date('Y');
    $year_plus = strtotime('+1 year', strtotime($year));
    $year_plus = date('Y', $year_plus);
    foreach ($bus_page->extra_calendar as $ec_item) {
        if ($ec_item->days && $ec_item->month) {
            $arr_extra_calendar_days = explode(',', $ec_item->days);
            foreach ($arr_extra_calendar_days as $day) {
                $arr_extra_calendar[] = [
                    'date' => $year . '-' . $ec_item->month . '-' . $day,
                    'extra_price' => $ec_item->extra_price,
                ];
                $arr_extra_calendar[] = [
                    'date' => $year_plus . '-' . $ec_item->month . '-' . $day,
                    'extra_price' => $ec_item->extra_price,
                ];
            }
        }
    }
    $needDate = $selected_date;
    foreach ($arr_extra_calendar as $row) {
      if (($row['date'] ?? null) === $needDate) {
        $calendar_extra_price = $row['extra_price'] ?? null;
        break;
      }
    }
}
 
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

$reestr_seat = '';
foreach ($arr_reserv_seat as $key => $val) {
$data_passenger = $pages->get('template=passengers, id=' . $val['id_passenger'] . '');
$phone_passenger = $data_passenger->phone_passenger;
$birthday_passenger = $data_passenger->birthday_passenger;
$reestr_seat .= '
    <p class="reestr_seat_item">
        Место - ' . $val['seat'] . ' - ' . $val['pay_or_booking'] . ' - ' . $val['confirm'] . '<br>
        Станция посадки: ' . $val['station'] . '<br>
        Станция высадки: ' . $val['station_finish'] . '<br>' . 
        $val['passenger'] . '<br>' . 
        $birthday_passenger . '<br>
        тип билета: ' . $val['type_ticket'] . '<br>' . 
        $val['passenger_doc'] . '<br>
        телефон: ' . $phone_passenger . '<br>
        агент: ' . $val['agent_ticket'] . '<br>
        комментарий: ' . $val['comment'] . '<br>
        <span> - Регистратор: ' . $val['operator'] . '</span>
    </p>
';
}

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

if ($bus_page->bonus_seats) {
    $max_seat = 63;
} else {
    $max_seat = 53;
}
$button_seat = '';
$sb_title_error = '';
for ($num_seat = 1; $num_seat <= $max_seat; $num_seat++) {
    $bonus_style = '';
    if ($num_seat >= 54) {
        $bonus_style = 'border: 1px dashed #000000 !important;';
    }
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
                    <button style="' . $bonus_style . '" class="uk-ticket-seat uk-margin-small-top uk-button uk-button-default seat_reserv" disabled title="Место забронировано: ' . $val['passenger'] . ', телефон: ' . $phone_passenger . ', станция посадки: ' . $val['station'] . ', станция высадки: ' . $val['station_finish'] . ', комментарий: ' . $val['comment'] . '">' . $val['seat'] . '' . $conf_status . '' . $sb_occupied . '' . $sb_error . '</button>
                    ';
                }
                if ($val['pay_or_booking'] == 'оплачено') {
                    $button_seat .= '
                    <button style="' . $bonus_style . '" class="uk-ticket-seat uk-margin-small-top uk-button uk-button-default seat_pay" disabled title="Место оплачено: ' . $val['passenger'] . ', телефон: ' . $phone_passenger . ', станция посадки: ' . $val['station'] . ', станция высадки: ' . $val['station_finish'] . ', комментарий: ' . $val['comment'] . '">' . $val['seat'] . '' . $conf_status . '' . $sb_occupied . '' . $sb_error . '</button>
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
        if ($sb_disabled != 'disabled') {
            $button_seat .= '
            <button style="' . $bonus_style . '" class="uk-ticket-seat uk-margin-small-top uk-button uk-button-default seat_free ' . $reserv_style . '">' . $num_seat . '' . $sb_occupied . '</button>
            ';
        } else {
            $button_seat .= '
            <button style="' . $bonus_style . '" class="uk-ticket-seat uk-margin-small-top uk-button uk-button-default seat_free" disabled>' . $num_seat . '' . $sb_occupied . '</button>
            ';
        }
    }
}

//ДИНАМИЧЕСКИЙ ПОИСК ПО ПАССАЖИРАМ
// $all_passengers = $pages->find('template=passengers');
// $arr_all_passengers = [];
// foreach ($all_passengers as $all_passengers_item) {
//     $arr_all_passengers[] = array(
//         "id_passenger"=>$all_passengers_item->id,
//         "name_passenger"=>$all_passengers_item->name_passenger,
//         "birthday_passenger"=>$all_passengers_item->birthday_passenger,
//         "type_doc_passenger"=>$all_passengers_item->type_doc_passenger,
//         "num_doc_passenger"=>$all_passengers_item->num_doc_passenger,
//         "passport_passenger"=>$all_passengers_item->passport_passenger,
//         "phone_passenger"=>$all_passengers_item->phone_passenger
//         );
// }
// //echo '<pre>'; print_r($arr_all_passengers); echo '</pre>';

// $passengers = '';
// foreach ($arr_all_passengers as $key => $val) {
// $passengers .= '
//     <p id="' . $val['id_passenger'] . '" class="passengers_item">' . $val['name_passenger'] . '<br><span>' . $val['birthday_passenger'] . ' — ' . $val['type_doc_passenger'] . ' — ' . $val['passport_passenger'] . ' — ' . $val['num_doc_passenger'] . '<br>' . $val['phone_passenger'] . '</span></p>
// ';
// }
//ДИНАМИЧЕСКИЙ ПОИСК ПО ПАССАЖИРАМ
?>

<div id="content">
    <h1 class="uk-heading-hero uk-text-center">Выбор места для регистрации билета</h1>
    <div class="uk-child-width-1-2@m" uk-grid>
        
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" uk-sticky>
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <h4 class="uk-margin-remove">Выбранный рейс:<br><span style="font-weight: 700;"><?php echo $selected_bus; ?></span></h4>
                <h4 class="uk-margin-remove">Дата: <span id="dispatch_date" style="font-weight: 700;"><?php echo $selected_date; ?></span> отправление<span style="font-weight: 700;"><?php echo $selected_time; ?></span></h4>
                <p class="uk-margin-remove uk-hidden" id="ep_sum"><?php echo $bus_page->extra_price; ?></p>
                <form class="uk-flex uk-flex-column" id="select_seat" action="/registratciia-bileta/" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="selected_bus" type="text" name="selected_bus" value="<?php echo $selected_bus; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="selected_id_bus" type="text" name="selected_id_bus" value="<?php echo $selected_id_bus; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="selected_date" type="text" name="selected_date" value="<?php echo $selected_date; ?>">
                    </div>
                    <p id="departure_date" class="uk-hidden"><?php echo strtotime($selected_date); ?></p>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="selected_time" type="text" name="selected_time" value="<?php echo $selected_time; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="selected_station_start" type="text" name="selected_station_start" value="" placeholder="Станция посадки" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input readonly" id="id_selected_station_start" type="text" name="id_selected_station_start" value="">
                    </div>

                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="selected_station_finish" type="text" name="selected_station_finish" value="" placeholder="Станция высадки" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input readonly" id="id_selected_station_finish" type="text" name="id_selected_station_finish" value="">
                    </div>

                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="selected_seat" type="text" name="selected_seat" value="" placeholder="Место" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top">
                        <select class="uk-select" id="pay_or_booking" name="pay_or_booking">
                            <option>забронировано</option>
                            <option>оплачено</option>
                        </select>
                    </div>
                    <div id="booking_sum_div" class="uk-margin-small-top">
                        <input class="uk-input" id="booking_sum" type="number" name="booking_sum" value="" placeholder="Сумма к оплате при бронировании" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top" style="display: none;">
                        <select class="uk-select" id="confirm" name="confirm">
                            <option>явился</option>
                            <option>не явился</option>
                        </select>
                    </div>
                    <div class="uk-margin-small-top">
                        <select class="uk-select" id="type_ticket" name="type_ticket">
                            <option>взрослый</option>
                            <option>детский</option>
                        </select>
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input readonly" id="selected_idpassenger" type="text" name="selected_idpassenger" value="" placeholder="ID пассажира" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="selected_name" type="text" name="selected_name" value="" placeholder="ФИО пассажира" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="selected_gender" type="text" name="selected_gender" value="" placeholder="Пол пассажира" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="selected_document" type="text" name="selected_document" value="" placeholder="Документ пассажира" autocomplete="off" required>
                    </div>
                    <?php
                    $all_agents = $pages->get('template=agents');
                    $agents = '';
                    foreach ($all_agents->agent_items as $agent_itm) {
                        $agents .= '
                        <option rate="' . $agent_itm->rate . '" diff="' . $agent_itm->difference . '">' . $agent_itm->agent . '</option>
                        ';
                    }
                    ?>
                    <div class="uk-margin-small-top">
                        <label for="agent_ticket">Агент</label>
                        <select class="uk-select" id="agent_ticket" name="agent_ticket">
                            <?php echo $agents; ?>
                        </select>
                    </div>
                    <div class="uk-margin-small-top">
                        <label for="price_ticket">Цена билета</label>
                        <input class="uk-input" id="price_ticket" type="number" name="price_ticket" value="" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top">
                        <label for="comment">Комментарий</label>
                        <input class="uk-input" id="comment" type="text" name="comment" value="" autocomplete="off">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="sb_idbus_forpost" type="text" name="sb_idbus_forpost" value="" autocomplete="off">
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button id="btn_for_reg" class="uk-margin-small-top uk-button uk-button-default" type="submit">Зарегистрировать</button>
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
                <h3 class="uk-margin-remove uk-card-title">Станции посадки</h3>
                <div class="uk-ticket-button-station-items start-station">
                    <?php echo $button_station_start;?>
                </div>
            </div>
            <br>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Станции высадки</h3>
                <div class="uk-ticket-button-station-items finish-station">
                    <?php echo $button_station_finish;?>
                </div>
            </div>
            <br>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column uk-hidden">
                <h3 class="uk-margin-remove uk-card-title">Таблица цен</h3>
                <div id="prices" class="uk-ticket-prices-items">
                    <?php echo $prices;?>
                </div>
                <h4 class="uk-margin-remove">Цена выбранного маршрута: <span id="sel_price"></span></h4>
            </div>
            <br>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column uk-hidden">
                <h3 class="uk-margin-remove uk-card-title">Надбавка по календарю</h3>
                <div id="prices" class="uk-ticket-prices-items">
                    <?php //print_r($arr_extra_calendar);?>
                </div>
                <h4 class="uk-margin-remove">Надбавка выбранного маршрута на <?php echo $selected_date;?>: <span id="ce_price"><?php echo $calendar_extra_price;?></span></h4>
            </div>
            <br>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Реестр уже купленных мест</h3>
                <div class="filter">
                    <div class="filter-elem">
                    <p class="filter_icon"><i class="fa-solid fa-filter"></i></p>
                    <input class="uk-input" id="search_tickets" type="text" name="search_tickets" placeholder="введите параметры для поиска">
                    </div>
                </div>
                <div class="reestr_seat uk-flex">
                    <?php echo $reestr_seat;?>
                </div>
            </div>
            <br>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <div class="sb_switсh">
                    <?php echo $switch_sb_connect;?>
                    <?php echo $switch_sb_bus;?>
                </div>
                <h3 class="uk-margin-remove uk-card-title">Свободные и занятые места</h3>
                <?php echo $sb_title_error;?>
                <div class="buttons_seat uk-flex uk-flex-wrap">
                    <?php echo $button_seat;?>
                </div>
            </div>
            <br>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" id="filter_passenger">
                <h3 class="uk-margin-remove uk-card-title">Выбор пассажира</h3>
                <p class="uk-margin-remove" style="color: red; font-weight: 700; line-height: 1;">После набора параметра нажмите Enter или кнопку "Фильтр" для фильтрации</p>
                <!-- <div class="uk-margin-small">
                    <input class="uk-input" id="search_passenger" type="text" name="search_passenger" placeholder="введите параметры для поиска">
                </div> -->
                <div class="uk-margin-small uk-flex uk-flex-middle">
                    <input class="uk-input" id="search_passenger" type="text" name="search_passenger" placeholder="введите параметры для фильтра по ФИО">
                    <p id="filter-passenger-btn" class="uk-margin-none uk-button uk-button-default">ФИЛЬТР</p>
                </div>
                <div class="uk-margin-small uk-flex uk-flex-middle">
                    <input class="uk-input" id="search_passenger_doc" type="text" name="search_passenger_doc" placeholder="введите параметры для фильтра по документу">
                    <p id="filter-passenger-doc-btn" class="uk-margin-none uk-button uk-button-default">ФИЛЬТР</p>
                </div>
                <div class="uk-margin-small uk-flex uk-flex-middle">
                    <input class="uk-input" id="search_passenger_phone" type="text" name="search_passenger_phone" placeholder="введите параметры для фильтра по телефону">
                    <p id="filter-passenger-phone-btn" class="uk-margin-none uk-button uk-button-default">ФИЛЬТР</p>
                </div>
                <div id="result-filter-passenger" class="reestr_passenger uk-flex">
                    <?php 
                    //ДИНАМИЧЕСКИЙ ПОИСК ПО ПАССАЖИРАМ
                    //echo $passengers; 
                    ?>
                </div>
                <div class="uk-margin-small-top uk-flex uk-flex-column">
                    <button class="uk-margin-small-top uk-button uk-button-default" type="button" uk-toggle="target: #modal-add_passenger">Добавить пассажира</button>
                </div>
            </div>
            <br>
            <?php echo $reestr_seat_1c;?>
        </div>
        
    </div>
    
    <!-- Модальное окно добавления нового пассажира-->
    <div id="modal-add_passenger" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Новый пассажир</h2>
            <div id="messages" class="messages-block">
                <p class="messages" style="color: green;"></p>
            </div>
                    
            <div class="uk-margin-small-top">
                <input class="uk-input" id="name_passenger" type="text" name="name_passenger" value="" placeholder="ФИО пассажира" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top">
                <label for="gender_passenger">Пол</label>
                <select class="uk-select" id="gender_passenger" name="gender_passenger" required>
                    <option value="">Выберите пол</option>
                    <option value="М">М</option>
                    <option value="Ж">Ж</option>
                </select>
            </div>
            <div class="uk-margin-small-top">
                <label for="gender_passenger">Гражданство</label>
                <select class="uk-select" id="citizenship_passenger" name="citizenship_passenger" required>
                    <option value="RU">Россия</option>
                    <option value="TJ">Таджикистан</option>
                    <option value="UZ">Узбекистан</option>
                    <option value="KG">Киргизия</option>
                    <option value="KZ">Казахстан</option>
                    <option value="BY">Беларусь</option>
                    <option value="UA">Украина</option>
                    <option value="AM">Армения</option>
                    <option value="AZ">Азербайджан</option>
                    <option value="IT">Италия</option>
                    <option value="ES">Испания</option>
                    <option value="FR">Франция</option>
                </select>
            </div>
            <div class="uk-margin-small-top">
                <label for="birthday_passenger">Дата рождения</label>
                <input class="uk-input" id="birthday_passenger" type="date" name="birthday_passenger" value="" placeholder="Дата рождения" autocomplete="off" required>
            </div>
            <!--<div class="uk-margin-small-top">-->
            <!--    <input class="uk-input" id="type_doc_passenger" type="text" name="type_doc_passenger" value="" placeholder="Тип документа" required>-->
            <!--</div>-->
            <div class="uk-margin-small-top">
                <label for="type_doc_passenger">Документ</label>
                <select class="uk-select" id="type_doc_passenger" name="type_doc_passenger" required>
                    <option value="">Выберите тип документа</option>
                    <option value="Паспорт РФ">Паспорт РФ</option>
                    <option value="Заграничный паспорт РФ">Заграничный паспорт РФ</option>
                    <option value="Паспорт иностранного пассажира">Паспорт иностранного пассажира</option>
                    <option value="Временное удостоверение ОВД">Временное удостоверение ОВД</option>
                    <option value="Свидетельство о рождении">Свидетельство о рождении</option>
                    <option value="Военный билет">Военный билет</option>
                    <option value="Вид на жительство">Вид на жительство</option>
                    <!-- <option value="Другой документ">Другой документ</option> -->
                </select>
            </div>
            <div class="uk-margin-small-top">
                <input class="uk-input" id="passport_passenger" type="text" name="passport_passenger" value="" placeholder="Серия документа" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top">
                <input class="uk-input" id="num_doc_passenger" type="text" name="num_doc_passenger" value="" placeholder="Номер документа" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top">
                <input class="uk-input" id="phone_passenger" type="text" name="phone_passenger" value="" placeholder="Телефон пассажира" autocomplete="off" required>
            </div>
            <!--
            <div class="uk-margin-small-top">
                <input class="uk-input" id="agent" type="text" name="agent" value="" placeholder="Агент" autocomplete="off">
            </div>
            -->

            <?php
            $all_agents = $pages->get('template=agents');
            $agents = '';
            foreach ($all_agents->agent_items as $agent_itm) {
                $agents .= '
                <option>' . $agent_itm->agent . '</option>
                ';
            }
            ?>
            <div class="uk-margin-small-top">
                <label for="agent">Агент</label>
                <select class="uk-select" id="agent" name="agent">
                    <option></option>
                    <?php echo $agents; ?>
                </select>
            </div>

            <br>
            <div class="uk-flex uk-flex-center">
                <button id="add_passenger" class="uk-button uk-button-primary" type="button">Добавить</button>
            </div>
        </div>
    </div>
</div>





<?php   
}
?>