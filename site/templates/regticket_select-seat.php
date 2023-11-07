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
$reserv_seat = $pages->find('template=purchased_tickets, id_bus=' . $selected_id_bus . ', date_depart=' . $selected_date . ',sort=seat');
$arr_reserv_seat = [];
foreach ($reserv_seat as $reserv_seat_item) {
    $arr_reserv_seat[] = array(
        "seat"=>$reserv_seat_item->seat,
        "pay_or_booking"=>$reserv_seat_item->pay_or_booking,
        "confirm"=>$reserv_seat_item->confirm,
        "id_passenger"=>$reserv_seat_item->id_passenger,
        "passenger"=>$reserv_seat_item->passenger,
        "passenger_doc"=>$reserv_seat_item->passenger_doc,
        "operator"=>$reserv_seat_item->operator
        );
}
//echo '<pre>'; print_r($arr_reserv_seat); echo '</pre>';

$reestr_seat = '';
foreach ($arr_reserv_seat as $key => $val) {
$data_passenger = $pages->get('template=passengers, id=' . $val['id_passenger'] . '');
$phone_passenger = $data_passenger->phone_passenger;
$reestr_seat .= '
    <p class="reestr_seat_item">Место - ' . $val['seat'] . ' - ' . $val['pay_or_booking'] . ' - ' . $val['confirm'] . '<br>' . $val['passenger'] . '<br>' . $val['passenger_doc'] . '<br>телефон:' . $phone_passenger . '<br><span> - Регистратор: ' . $val['operator'] . '</span></p>
';
}

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
                    <button class="uk-ticket-seat uk-margin-small-top uk-button uk-button-default seat_reserv" disabled title="Место забронировано: ' . $val['passenger'] . ': ' . $phone_passenger . '">' . $val['seat'] . '' . $conf_status . '</button>
                    ';
                }
                if ($val['pay_or_booking'] == 'оплачено') {
                    $button_seat .= '
                    <button class="uk-ticket-seat uk-margin-small-top uk-button uk-button-default seat_pay" disabled title="Место оплачено: ' . $val['passenger'] . ': ' . $phone_passenger . '">' . $val['seat'] . '' . $conf_status . '</button>
                    ';
                }
        }
    }
    if ($free == true) {
        if ($num_seat < 10) {
            $num_seat = '0' . $num_seat;
        }
        $button_seat .= '
        <button class="uk-ticket-seat uk-margin-small-top uk-button uk-button-default seat_free">' . $num_seat . '</button>
        ';
    }
}

$all_passengers = $pages->find('template=passengers');
$arr_all_passengers = [];
foreach ($all_passengers as $all_passengers_item) {
    $arr_all_passengers[] = array(
        "id_passenger"=>$all_passengers_item->id,
        "name_passenger"=>$all_passengers_item->name_passenger,
        "birthday_passenger"=>$all_passengers_item->birthday_passenger,
        "type_doc_passenger"=>$all_passengers_item->type_doc_passenger,
        "num_doc_passenger"=>$all_passengers_item->num_doc_passenger,
        "passport_passenger"=>$all_passengers_item->passport_passenger,
        "phone_passenger"=>$all_passengers_item->phone_passenger
        );
}
//echo '<pre>'; print_r($arr_all_passengers); echo '</pre>';

$passengers = '';
foreach ($arr_all_passengers as $key => $val) {
$passengers .= '
    <p id="' . $val['id_passenger'] . '" class="passengers_item">' . $val['name_passenger'] . '<br><span>' . $val['birthday_passenger'] . ' - ' . $val['type_doc_passenger'] . ' - ' . $val['passport_passenger'] . ' - ' . $val['num_doc_passenger'] . '<br>' . $val['phone_passenger'] . '</span></p>
';
}
?>

<div id="content">
	<h1 class="uk-heading-hero uk-text-center">Выбор места для регистрации билета</h1>
	<div class="uk-child-width-1-2@m" uk-grid>
	    
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" uk-sticky>
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <h4 class="uk-margin-remove">Выбранный рейс:<br><span style="font-weight: 700;"><?php echo $selected_bus; ?></span></h4>
                <h4 class="uk-margin-remove">Дата: <span style="font-weight: 700;"><?php echo $selected_date; ?></span> отправление<span style="font-weight: 700;"><?php echo $selected_time; ?></span></h4>
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
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="selected_time" type="text" name="selected_time" value="<?php echo $selected_time; ?>">
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
                    <div class="uk-margin-small-top">
                        <select class="uk-select" id="confirm" name="confirm">
                            <option>не подтверждено</option>
                            <option>подтверждено</option>
                        </select>
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input readonly" id="selected_idpassenger" type="text" name="selected_idpassenger" value="" placeholder="ID пассажира" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="selected_name" type="text" name="selected_name" value="" placeholder="ФИО пассажира" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="selected_document" type="text" name="selected_document" value="" placeholder="Документ пассажира" autocomplete="off" required>
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Зарегистрировать</button>
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
                <h3 class="uk-margin-remove uk-card-title">Реестр уже купленных мест</h3>
                <div class="reestr_seat uk-flex">
                    <?php echo $reestr_seat ; ?>
                </div>
            </div>
            <br>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Свободные и занятые места</h3>
                <div class="buttons_seat uk-flex uk-flex-wrap">
                    <?php echo $button_seat; ?>
                </div>
            </div>
            <br>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Выбор пассажира</h3>
                <div class="uk-margin-small">
                    <input class="uk-input" id="search_passenger" type="text" name="search_passenger" placeholder="введите параметры для поиска">
                </div>
                <div class="reestr_passenger uk-flex">
                    <?php echo $passengers; ?>
                </div>
                <div class="uk-margin-small-top uk-flex uk-flex-column">
                    <button class="uk-margin-small-top uk-button uk-button-default" type="button" uk-toggle="target: #modal-add_passenger">Добавить пассажира</button>
                </div>
            </div>
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
                    <option value="Свидетельство о рождении">Свидетельство о рождении</option>
                    <option value="Военный билет">Военный билет</option>
                    <option value="Другой документ">Другой документ</option>
                </select>
            </div>
            <div class="uk-margin-small-top">
                <input class="uk-input" id="passport_passenger" type="text" name="passport_passenger" value="" placeholder="Серия документа" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top">
                <input class="uk-input" id="num_doc_passenger" type="text" name="num_doc_passenger" value="" placeholder="Номер документа" autocomplete="off" required>
            </div>
            <br>
            <div class="uk-margin-small-top">
                <input class="uk-input" id="phone_passenger" type="text" name="phone_passenger" value="" placeholder="Телефон пассажира" autocomplete="off" required>
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