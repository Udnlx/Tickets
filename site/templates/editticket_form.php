<?php namespace ProcessWire;

$access = '';
if(isset($_SESSION['access'])){
    $access = $_SESSION['access'];
}

$sb_idbus = $_POST['sb_idbus'];
$id_seat = !empty($_POST['id_seat'])?$_POST['id_seat']:NULL;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator' || $access == 'agent') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Правка билета</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php

$ticket = $pages->get('template=purchased_tickets, id=' . $id_seat . '');

$sb_status = '';
$sb_btn_disabled = '';
if ($ticket->sb_ticket_id) {
    $sb_status = '
    <p class="uk-margin-remove uk-text-success uk-text-bold uk-text-center">Статус: Билет проведен в 1С</p>
    <p class="uk-margin-remove uk-text-success uk-text-bold uk-text-center">ID билета в 1С: ' . $ticket->sb_ticket_id . '</p>
    ';
    $sb_btn_disabled = 'disabled';
} else {
    $sb_status = '
    <p class="uk-margin-remove uk-text-warning uk-text-bold uk-text-center">Статус: Билет не проведен в 1С</p>
    ';
    $sb_btn_disabled = '';
}

$old_start_station = preg_split('/[—]/u', $ticket->name_station, -1, PREG_SPLIT_NO_EMPTY);
$old_finish_station = preg_split('/[—]/u', $ticket->name_station_finish, -1, PREG_SPLIT_NO_EMPTY);

$button_station_start = '';
$bus_page = $pages->get('id=' . $ticket->id_bus . '');
foreach ($bus_page->station_start as $item) {
$array = preg_split('/[—]/u', $item->title, -1, PREG_SPLIT_NO_EMPTY);
//print_r($array);
$button_station_start .= '
<button id="' . $item->id . '" param_btn="' . $item->title . '" class="uk-ticket-button-station-start uk-margin-small-top uk-button uk-button-default">' . $array[0] . '</button>
';
}

$button_station_finish = '';
$bus_page = $pages->get('id=' . $ticket->id_bus . '');
foreach ($bus_page->station_finish as $item) {
$array = preg_split('/[—]/u', $item->title, -1, PREG_SPLIT_NO_EMPTY);
//print_r($array);
$button_station_finish .= '
<button id="' . $item->id . '" param_btn="' . $item->title . '" class="uk-ticket-button-station-finish uk-margin-small-top uk-button uk-button-default">' . $array[0] . '</button>
';
}

$prices = '';
$bus_page = $pages->get('id=' . $ticket->id_bus . '');
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

?>

<div id="content">
	<h1 class="uk-heading-hero uk-text-center">Правка билета</h1>
	<div class="uk-child-width-1-2@m" uk-grid>
        
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" uk-sticky>
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <h4 class="uk-margin-remove">Рейс:<br><span style="font-weight: 700;"><?php echo $ticket->bus; ?></span></h4>
                <h4 class="uk-margin-remove">Дата: <span style="font-weight: 700;"><?php echo $ticket->date_depart; ?></span> отправление<span style="font-weight: 700;"><?php echo $ticket->time_depart; ?></span></h4>
                <h4 class="uk-margin-remove">Место: <span style="font-weight: 700;"><?php echo $ticket->seat; ?></span></h4>
                <h4 class="uk-margin-remove">Пассажир: <span style="font-weight: 700;"><?php echo $ticket->passenger; ?></span></h4>
                <h4 class="uk-margin-remove">ID билета: <span style="font-weight: 700;"><?php echo $id_seat; ?></span></h4>
                <form class="uk-flex uk-flex-column" id="edit_ticket" action="/pravka-bileta-smena-statusa/" method="post">

                    <input class="uk-input readonly uk-hidden" id="id_edit_ticket" type="text" name="id_edit_ticket" value="<?php echo $id_seat; ?>">
                    
                    <div class="uk-margin-small-top">
                        <p class="old-value">Старое значение: <span><?php echo $old_start_station[0]; ?></span></p>
                        <input class="uk-input readonly uk-hidden" id="old_station_start" type="text" name="old_station_start" value="<?php echo $old_start_station[0]; ?>">
                        <input class="uk-input readonly" id="selected_station_start" type="text" name="selected_station_start" value="" placeholder="Станция посадки" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input readonly" id="id_selected_station_start" type="text" name="id_selected_station_start" value="">
                    </div>

                    <div class="uk-margin-small-top">
                        <p class="old-value">Старое значение: <span><?php echo $old_finish_station[0]; ?></span></p>
                        <input class="uk-input readonly uk-hidden" id="old_station_finish" type="text" name="old_station_finish" value="<?php echo $old_finish_station[0]; ?>">
                        <input class="uk-input readonly" id="selected_station_finish" type="text" name="selected_station_finish" value="" placeholder="Станция высадки" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input readonly" id="id_selected_station_finish" type="text" name="id_selected_station_finish" value="">
                    </div>

                    <div class="uk-margin-small-top">
                        <p class="old-value">Старое значение: <span><?php echo $ticket->pay_or_booking; ?></span></p>
                        <input class="uk-input readonly uk-hidden" id="old_pay_or_booking" type="text" name="old_pay_or_booking" value="<?php echo $ticket->pay_or_booking; ?>">
                        <select class="uk-select" id="pay_or_booking" name="pay_or_booking">
                            <option>забронировано</option>
                            <option>оплачено</option>
                        </select>
                    </div>
                    <div id="booking_sum_div" class="uk-margin-small-top">
                        <p class="old-value">Старое значение: <span><?php echo $ticket->booking_sum; ?></span></p>
                        <input class="uk-input readonly uk-hidden" id="old_booking_sum" type="text" name="old_booking_sum" value="<?php echo $ticket->booking_sum; ?>">
                        <input class="uk-input" id="booking_sum" type="number" name="booking_sum" value="" placeholder="Сумма к оплате при бронировании" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top">
                        <select class="uk-select" id="confirm" name="confirm">
                            <option>явился</option>
                            <option>не явился</option>
                        </select>
                    </div>
                    <div class="uk-margin-small-top">
                        <p class="old-value">Старое значение: <span><?php echo $ticket->type_ticket; ?></span></p>
                        <input class="uk-input readonly uk-hidden" id="old_type_ticket" type="text" name="old_type_ticket" value="<?php echo $ticket->type_ticket; ?>">
                        <select class="uk-select" id="type_ticket" name="type_ticket">
                            <option>взрослый</option>
                            <option>детский</option>
                        </select>
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
                        <p class="old-value">Старое значение: <span><?php echo $ticket->agent_ticket; ?></span></p>
                        <input class="uk-input readonly uk-hidden" id="old_agent_ticket" type="text" name="old_agent_ticket" value="<?php echo $ticket->agent_ticket; ?>">
                        <select class="uk-select" id="agent_ticket" name="agent_ticket">
                            <?php echo $agents; ?>
                        </select>
                    </div>
                    <div class="uk-margin-small-top">
                        <label for="price_ticket">Цена билета</label>
                        <p class="old-value">Старое значение: <span><?php echo $ticket->price_ticket; ?></span></p>
                        <input class="uk-input readonly uk-hidden" id="old_price_ticket" type="text" name="old_price_ticket" value="<?php echo $ticket->price_ticket; ?>">
                        <input class="uk-input" id="price_ticket" type="number" name="price_ticket" value="" autocomplete="off" required>
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Внести правки</button>
                        <a class="uk-margin-small uk-button uk-button-default" href="/pravka-bileta-vybor-reisa/">Выбрать другой рейс и место</a>
                        <a class="uk-margin-small uk-button uk-button-default" href="/">Вернутся на главную</a>
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
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Провести билет в 1С</h3>
                <p class="uk-margin-remove uk-text-danger uk-text-bold uk-text-center">Внимание! При подтверждении, билет будет проведен в системе 1С.</p>
                <?php echo $sb_status; ?>
                <form class="uk-flex uk-flex-column" id="reg_ticket" action="/pravka-bileta-registratciia-v-1s/" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="sb_idbus" type="text" name="sb_idbus" value="<?php echo $sb_idbus; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="sb_reg_ticket" type="text" name="sb_reg_ticket" value="<?php echo $id_seat; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Провести билет в 1С</button>
                    </div>
                </form>
            </div>
            <br>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Удалить билет из 1С</h3>
                <p class="uk-margin-remove uk-text-danger uk-text-bold uk-text-center">Внимание! При подтверждении, билет будет удален из системы 1С. Но останется купленным в этой системе. Для удаления билета из этой системы воспользуйтесь функционалом "Освободить место" ниже</p>
                <?php echo $sb_status; ?>
                <form class="uk-flex uk-flex-column" id="del_ticket" action="/pravka-bileta-udalenie-iz-1s/" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="sb_idbus" type="text" name="sb_idbus" value="<?php echo $sb_idbus; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="sb_del_ticket" type="text" name="sb_del_ticket" value="<?php echo $id_seat; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Удалить билет из 1С</button>
                    </div>
                </form>
            </div>
            <br>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Освободить место</h3>
                <p class="uk-margin-remove uk-text-danger uk-text-bold uk-text-center">Внимание! Прежде чем освобождать место, убедитесь, что билет удален из системы 1С и место является свободным.</p>
                <p class="uk-margin-remove uk-text-danger uk-text-bold uk-text-center">Внимание! Операция освобождения места безвозвратна, все данные по выбранному месту будут удалены и место освободится.</p>
                <?php echo $sb_status; ?>
                <form class="uk-flex uk-flex-column" id="delete_ticket" action="/pravka-bileta-udalenie/" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_selected_bus" type="text" name="del_selected_bus" value="<?php echo $ticket->bus; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_selected_id_bus" type="text" name="del_selected_id_bus" value="<?php echo $ticket->id_bus; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_selected_date" type="text" name="del_selected_date" value="<?php echo $ticket->date_depart; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_selected_time" type="text" name="del_selected_time" value="<?php echo $ticket->time_depart; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_selected_seat" type="text" name="del_selected_seat" value="<?php echo $ticket->seat; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_id_seat" type="text" name="del_id_seat" value="<?php echo $id_seat; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_passenger" type="text" name="del_passenger" value="<?php echo $ticket->passenger; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button class="uk-margin-small-top uk-button uk-button-default" <?php echo $sb_btn_disabled; ?> type="submit">Освободить место</button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
        
    </div>
</div>

<?php   
}
?>