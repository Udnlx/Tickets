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
$selected_station_start = !empty($_POST['selected_station_start'])?$_POST['selected_station_start']:NULL;
$id_selected_station_start = !empty($_POST['id_selected_station_start'])?$_POST['id_selected_station_start']:NULL;
$selected_station_finish = !empty($_POST['selected_station_finish'])?$_POST['selected_station_finish']:NULL;
$id_selected_station_finish = !empty($_POST['id_selected_station_finish'])?$_POST['id_selected_station_finish']:NULL;

$selected_seat = !empty($_POST['selected_seat'])?$_POST['selected_seat']:NULL;
$pay_or_booking = !empty($_POST['pay_or_booking'])?$_POST['pay_or_booking']:NULL;
$booking_sum = !empty($_POST['booking_sum'])?$_POST['booking_sum']:NULL;
$confirm = !empty($_POST['confirm'])?$_POST['confirm']:NULL;
$type_ticket = !empty($_POST['type_ticket'])?$_POST['type_ticket']:NULL;
$selected_idpassenger = !empty($_POST['selected_idpassenger'])?$_POST['selected_idpassenger']:NULL;
$selected_name = !empty($_POST['selected_name'])?$_POST['selected_name']:NULL;
$selected_document = !empty($_POST['selected_document'])?$_POST['selected_document']:NULL;
$lite_selected_document = mb_substr($selected_document, -2, 2);
$agent_ticket = !empty($_POST['agent_ticket'])?$_POST['agent_ticket']:NULL;
$price_ticket = !empty($_POST['price_ticket'])?$_POST['price_ticket']:NULL;
$comment = !empty($_POST['comment'])?$_POST['comment']:NULL;

$sb_idbus_forreg = $_POST['sb_idbus_forpost'];
$sb_button_reg = '';
if ($sb_idbus_forreg != '') {
    $sb_button_reg = '
    <div class="uk-margin-small-top uk-flex uk-flex-column">
        <button id="sb_reg_ticket" class="uk-margin-small-top uk-button uk-button-danger" type="butoon">Регистрация билета в 1C</button>
    </div>
    ';
}
$page_passenger = $pages->get('template=passengers, id=' . $selected_idpassenger . '');
$sb_birthday = $page_passenger->birthday_passenger;
$sb_doc = $page_passenger->type_doc_passenger;
$sb_docnum = $page_passenger->num_doc_passenger;
$sb_docseries = $page_passenger->passport_passenger;
$sb_namepassenger = $page_passenger->name_passenger;
$sb_gender = $page_passenger->gender_passenger;
$sb_citizenship = $page_passenger->citizenship_passenger;
$sb_phone = $page_passenger->phone_passenger;

$success = 'Билет успешно зарегистрирован';
if ($selected_bus && $selected_id_bus && $selected_date && $selected_time && $selected_seat && $selected_name && $selected_document && $operator != 'no_operator') {
    //echo $selected_bus . $selected_id_bus . $selected_date . $selected_time . $selected_seat . $selected_name . $selected_document . $selected_idpassenger;
    $ticket_page = $pages->get('title=' . $selected_bus . ' - ' . $selected_date . ' ' . $selected_time . ' место-' . $selected_seat . '');
    if ($ticket_page->id > 0) {
        $success = 'Билет не зарегистрирован!<br>Такой билет уже существует<br>ID-' . $ticket_page->id;
        $ticket_id = 'Билет не зарегистрирован!';
    } else {
        $pages->add('purchased_tickets', 1026 , [
        'title' => $selected_bus . ' - ' . $selected_date . ' ' . $selected_time . ' место-' . $selected_seat,
        'bus' => $selected_bus,
        'id_bus' => $selected_id_bus,
        'date_depart' => $selected_date,
        'time_depart' => $selected_time,
        'id_station' => $id_selected_station_start,
        'name_station' => $selected_station_start,
        'id_station_finish' => $id_selected_station_finish,
        'name_station_finish' => $selected_station_finish,
        'seat' => $selected_seat,
        'pay_or_booking' => $pay_or_booking,
        'booking_sum' => $booking_sum,
        'confirm' => $confirm,
        'type_ticket' => $type_ticket,
        'id_passenger' => $selected_idpassenger,
        'passenger' => $selected_name,
        'passenger_doc' => $selected_document,
        'operator' => $operator,
        'agent_ticket' => $agent_ticket,
        'price_ticket' => $price_ticket,
        'comment' => $comment,
        ]);

        $ticket_page = $pages->get('title=' . $selected_bus . ' - ' . $selected_date . ' ' . $selected_time . ' место-' . $selected_seat . '');
        $ticket_id = $ticket_page->id;
        $log = '';
        $log .= date('Y-m-d H:i:s') . ' - Зарегистрирован билет id - ' . $ticket_id . ', оператором ' . $operator . '. ';
        $log .= 'Автобус ' . $selected_bus . ', дата отправления ' . $selected_date . ', место посадки ' . $selected_seat . '. '; 
        file_put_contents(__DIR__ . '/log_agent_tikets.txt', $log . PHP_EOL, FILE_APPEND);
    }
} else {
    $success = 'Билет не зарегистрирован!<br>Ошибка в данных';
}



if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Билет не зарегистрирован</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {

$print_ticket = '';
if ($operator == 'Котельники') {
    $print_ticket = '
    <form class="uk-flex uk-flex-column" id="print_ticket" action="/pechat-bileta/" method="post">
        <div class="uk-margin-small-top uk-hidden">
            <input class="uk-input readonly" id="print_ticket_id" type="text" name="print_ticket_id" value="' . $ticket_id . '" placeholder="ID билета" autocomplete="off" required>
        </div>
        
        <div class="uk-margin-small-top uk-flex uk-flex-column">
            <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Распечатать билет</button>
        </div>
    </form>
    ';
}
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center"><?php echo $success; ?></h1>
	
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
        <h4 class="uk-margin-remove">Данные о билете:</h4>
        <p class="uk-margin-remove">ID билета: <span style="font-weight: 700;"><?php echo $ticket_id; ?></span></p>
        <p class="uk-margin-remove">Автобус: <span style="font-weight: 700;"><?php echo $selected_bus; ?></span></p>
        <p class="uk-margin-remove">ID автобуса: <span style="font-weight: 700;"><?php echo $selected_id_bus; ?></span></p>
        <p class="uk-margin-remove">Дата и время отправления: <span style="font-weight: 700;"><?php echo $selected_date; ?> <?php echo $selected_time; ?></span></p>
        <p class="uk-margin-remove">Станция и время посадки: <span style="font-weight: 700;"><?php echo $selected_station_start; ?></span></p>
        <p class="uk-margin-remove">ID cтанции посадки: <span style="font-weight: 700;"><?php echo $id_selected_station_start; ?></span></p>
        <p class="uk-margin-remove">Станция и время высадки: <span style="font-weight: 700;"><?php echo $selected_station_finish; ?></span></p>
        <p class="uk-margin-remove">ID cтанции высадки: <span style="font-weight: 700;"><?php echo $id_selected_station_finish; ?></span></p>
        <p class="uk-margin-remove">Место: <span style="font-weight: 700;"><?php echo $selected_seat; ?></span></p>
        <p class="uk-margin-remove">Статус: <span style="font-weight: 700;"><?php echo $pay_or_booking; ?></span></p>
        <p class="uk-margin-remove">Статус подтверждения: <span style="font-weight: 700;"><?php echo $confirm; ?></span></p>
        <p class="uk-margin-remove">Тип билета: <span style="font-weight: 700;"><?php echo $type_ticket; ?></span></p>
        <p class="uk-margin-remove">ФИО пассажира: <span style="font-weight: 700;"><?php echo $selected_name; ?></span></p>
        <p class="uk-margin-remove">Последние цифры документа пассажира: <span style="font-weight: 700;">.....<?php echo $lite_selected_document; ?></span></p>
        <p class="uk-margin-remove">Агент билета: <span style="font-weight: 700;"><?php echo $agent_ticket; ?></span></p>
        <p class="uk-margin-remove">Цена билета: <span style="font-weight: 700;"><?php echo $price_ticket; ?></span></p>
        <p class="uk-margin-remove">Комментарий: <span style="font-weight: 700;"><?php echo $comment; ?></span></p>

        <form class="uk-flex uk-flex-column" id="select_bus" action="/agent-registratciia-bileta-vybor-mesta/" method="post">
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="post_bus" type="text" name="post_bus" value="<?php echo $selected_bus ; ?>">
            </div>
            
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="post_id_bus" type="text" name="post_id_bus" value="<?php echo $selected_id_bus ; ?>">
            </div>
            
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="post_time" type="text" name="post_time" value="<?php echo $selected_time ; ?>">
            </div>
            
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="post_date" type="date" name="post_date" value="<?php echo $selected_date ; ?>">
            </div>
            
            <div class="uk-margin-small-top uk-flex uk-flex-column">
                <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Оформить еще один билет на этот же рейс</button>
            </div>
        </form>

        <?php echo $print_ticket; ?>

        <!--
        <form class="uk-flex uk-flex-column" id="print_ticket" action="/pechat-bileta/" method="post">
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input readonly" id="print_ticket_id" type="text" name="print_ticket_id" value="<?php echo $ticket_id ; ?>" placeholder="ID билета" autocomplete="off" required>
            </div>
            
            <div class="uk-margin-small-top uk-flex uk-flex-column">
                <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Распечатать билет</button>
            </div>
        </form>
        -->

        <div class="uk-margin-small-top uk-hidden">
            <input class="uk-input" id="sb_idbus" type="text" name="sb_idbus" value="<?php echo $sb_idbus_forreg ; ?>">
            <input class="uk-input" id="sb_seat" type="text" name="sb_seat" value="<?php echo $selected_seat ; ?>">
            <input class="uk-input" id="sb_birthday" type="text" name="sb_birthday" value="<?php echo $sb_birthday ; ?>">
            <input class="uk-input" id="sb_doc" type="text" name="sb_doc" value="<?php echo $sb_doc ; ?>">
            <input class="uk-input" id="sb_docnum" type="text" name="sb_docnum" value="<?php echo $sb_docnum ; ?>">
            <input class="uk-input" id="sb_docseries" type="text" name="sb_docseries" value="<?php echo $sb_docseries ; ?>">
            <input class="uk-input" id="sb_passengername" type="text" name="sb_passengername" value="<?php echo $sb_namepassenger ; ?>">
            <input class="uk-input" id="sb_gender" type="text" name="sb_gender" value="<?php echo $sb_gender ; ?>">
            <input class="uk-input" id="sb_citizenship" type="text" name="sb_citizenship" value="<?php echo $sb_citizenship ; ?>">
            <input class="uk-input" id="sb_phone" type="text" name="sb_phone" value="<?php echo $sb_phone ; ?>">
            <input class="uk-input" id="sb_idticket" type="text" name="sb_idticket" value="<?php echo $ticket_page->id ; ?>">
            <input class="uk-input" id="sb_operator" type="text" name="sb_operator" value="агент, <?php echo $operator; ?>">
        </div>
        <?php echo $sb_button_reg ; ?>
        <div id="reg_messages" class="messages-block" style="margin: 10px 0 0 0;">
            <p class="messages" style="color: green;"></p>
        </div>

        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>
	
<?php   
}
?>