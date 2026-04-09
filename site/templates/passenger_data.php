<?php namespace ProcessWire;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

$passenger_idpassenger = !empty($_POST['passenger_idpassenger'])?$_POST['passenger_idpassenger']:NULL;  
$passenger_name = !empty($_POST['passenger_name'])?$_POST['passenger_name']:NULL;  
$passenger_document = !empty($_POST['passenger_document'])?$_POST['passenger_document']:NULL;  

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Данные о пассажире</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
$reserv_seat = $pages->find('template=purchased_tickets, id_passenger=' . $passenger_idpassenger . ',sort=seat');
$arr_reserv_seat = [];
foreach ($reserv_seat as $reserv_seat_item) {
    $arr_reserv_seat[] = array(
        "bus"=>$reserv_seat_item->bus,
        "date_depart"=>$reserv_seat_item->date_depart,
        "time_depart"=>$reserv_seat_item->time_depart,
        "seat"=>$reserv_seat_item->seat,
        "pay_or_booking"=>$reserv_seat_item->pay_or_booking,
        "confirm"=>$reserv_seat_item->confirm,
        "station"=>$reserv_seat_item->name_station,
        "passenger"=>$reserv_seat_item->passenger,
        "passenger_doc"=>$reserv_seat_item->passenger_doc,
        "operator"=>$reserv_seat_item->operator
        );
}
//echo '<pre>'; print_r($arr_reserv_seat); echo '</pre>';

$reestr_seat = '';
foreach ($arr_reserv_seat as $key => $val) {
$reestr_seat .= '
    <p class="reestr_seat_item">' . $val['bus'] . ' - ' . $val['date_depart'] . ' - ' . $val['time_depart'] . '<br> Станция посадки: ' . $val['station'] . '<br>Место - ' . $val['seat'] . ' - ' . $val['pay_or_booking'] . ' - ' . $val['confirm'] . ' - ' . $val['passenger'] . ' - ' . $val['passenger_doc'] . '<br><span> - Регистратор: ' . $val['operator'] . '</span></p>
';
}
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Данные о пассажире</h1>
	
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
        <p class="uk-margin-remove">ID: <span style="font-weight: 700;"><?php echo $passenger_idpassenger; ?></span></p>
        <p class="uk-margin-remove">ФИО пассажира: <span style="font-weight: 700;"><?php echo $passenger_name; ?></span></p>
        <p class="uk-margin-remove">Документ пассажира: <span style="font-weight: 700;"><?php echo $passenger_document; ?></span></p>
        
        <a class="uk-margin-small uk-button uk-button-default" href="/reestr-passazhirov-vybor-passazhira/">Выбрать другого пассажира</a>
        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
    
    <h2 class="uk-heading-hero uk-text-center">Правка пассажира</h2>
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        
        <p class="uk-text-warning uk-text-bold uk-text-center">Внимание! В этой форме правятся данные только текущего пассажира, если нужно добавить нового пассажира, то прежде удалите пассажира на этой странице, а затем добавьте нового пассажира на предыдущей странице или при регистрации билета.</p>
        <div class="uk-margin-small-top uk-flex uk-flex-column">
            <button class="uk-margin-small-top uk-button uk-button-default" type="button" uk-toggle="target: #modal-edit_passenger">Внести правки</button>
        </div>
        
        <p class="uk-text-warning uk-text-bold uk-text-center">Внимание! Удаление пассажира безвозвратно, все данные по выбранному пассажиру будут удалены. Купленные билеты не затрагиваются.</p>
        <form class="uk-flex uk-flex-column" id="delpassenger" action="/reestr-passazhirov-udalenie-passazhira/" method="post">
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="passenger_idpassenger" type="text" name="passenger_idpassenger" value="<?php echo $passenger_idpassenger; ?>">
            </div>
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="passenger_name" type="text" name="passenger_name" value="<?php echo $passenger_name; ?>">
            </div>
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="passenger_document" type="text" name="passenger_document" value="<?php echo $passenger_document; ?>">
            </div>
            
            <div class="uk-margin-small-top uk-flex uk-flex-column">
                <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Удалить пассажира</button>
            </div>
        </form>
        
    </div>
    
    <h2 class="uk-heading-hero uk-text-center">Данные о покупках пассажира</h2>
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <div class="filter">
            <div class="filter-elem">
            <p class="filter_icon"><i class="fa-solid fa-filter"></i></p>
            <input class="uk-input" id="search_tickets" type="text" name="search_tickets" placeholder="введите параметры для поиска">
            </div>
        </div>
        <div class="reestr_seat uk-flex">
            <?php echo $reestr_seat ; ?>
        </div>
    </div>
    
    <!-- Модальное окно правки пассажира-->
    <?php 
    $page_passenger = $pages->get('template=passengers, id=' . $passenger_idpassenger . ''); 
    $time = strtotime($page_passenger->birthday_passenger);
    $dayformat = date('Y-m-d',$time);
    ?>
    <div id="modal-edit_passenger" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Правка пассажира</h2>
            <div id="edit_messages" class="messages-block">
                <p class="messages" style="color: green;"></p>
            </div>
            
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input readonly" id="id_passenger" type="text" name="id_passenger" value="<?php echo $page_passenger->id ?>" autocomplete="off">
            </div>        
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input readonly" id="old_name_passenger" type="text" name="old_name_passenger" value="<?php echo $page_passenger->name_passenger ?>" placeholder="ФИО пассажира" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input readonly" id="old_gender_passenger" type="text" name="old_gender_passenger" value="<?php echo $page_passenger->gender_passenger ?>" placeholder="Пол" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input readonly" id="old_citizenship_passenger" type="text" name="old_citizenship_passenger" value="<?php echo $page_passenger->citizenship_passenger ?>" placeholder="Пол" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input readonly" id="old_birthday_passenger" type="date" name="old_birthday_passenger" value="<?php echo $dayformat ?>" placeholder="Дата рождения" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input readonly" id="old_type_doc_passenger" type="text" name="old_type_doc_passenger" value="<?php echo $page_passenger->type_doc_passenger ?>" placeholder="Тип документа" required>
            </div>
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input readonly" id="old_num_doc_passenger" type="text" name="old_num_doc_passenger" value="<?php echo $page_passenger->num_doc_passenger ?>" placeholder="Серия и номер" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input readonly" id="old_passport_passenger" type="text" name="old_passport_passenger" value="<?php echo $page_passenger->passport_passenger ?>" placeholder="Параметры документа" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input readonly" id="old_phone_passenger" type="text" name="old_phone_passenger" value="<?php echo $page_passenger->phone_passenger ?>" placeholder="Телефон пассажира" autocomplete="off" required>
            </div>
            
            <div class="uk-margin-small-top">
                <input class="uk-input" id="name_passenger" type="text" name="name_passenger" value="<?php echo $page_passenger->name_passenger ?>" placeholder="ФИО пассажира" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top">
                <label for="gender_passenger">Пол</label>
                <select class="uk-select" id="gender_passenger" name="gender_passenger" required>
                    <option value="<?php echo $page_passenger->gender_passenger ?>"><?php echo $page_passenger->gender_passenger ?></option>
                    <option value="М">М</option>
                    <option value="Ж">Ж</option>
                </select>
            </div>
            <div class="uk-margin-small-top">
                <label for="gender_passenger">Гражданство</label>
                <select class="uk-select" id="citizenship_passenger" name="citizenship_passenger" required>
                    <option value="<?php echo $page_passenger->citizenship_passenger ?>"><?php echo $page_passenger->citizenship_passenger ?></option>
                    <option value="RU">Россия</option>
                    <option value="TJ">Таджикистан</option>
                    <option value="UZ">Узбекистан</option>
                    <option value="KG">Киргизия</option>
                    <option value="KZ">Казахстан</option>
                    <option value="BY">Беларусь</option>
                    <option value="UA">Украина</option>
                    <option value="AM">Армения</option>
                    <option value="IT">Италия</option>
                    <option value="ES">Испания</option>
                    <option value="FR">Франция</option>
                </select>
            </div>
            <div class="uk-margin-small-top">
                <input class="uk-input" id="birthday_passenger" type="date" name="birthday_passenger" value="<?php echo $dayformat ?>" placeholder="Дата рождения" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top">
                <label for="type_doc_passenger">Документ</label>
                <select class="uk-select" id="type_doc_passenger" name="type_doc_passenger" required>
                    <option value="<?php echo $page_passenger->type_doc_passenger ?>"><?php echo $page_passenger->type_doc_passenger ?></option>
                    <option value="Паспорт РФ">Паспорт РФ</option>
                    <option value="Заграничный паспорт РФ">Заграничный паспорт РФ</option>
                    <option value="Паспорт иностранного пассажира">Паспорт иностранного пассажира</option>
                    <option value="Свидетельство о рождении">Свидетельство о рождении</option>
                    <option value="Военный билет">Военный билет</option>
                    <option value="Вид на жительство">Вид на жительство</option>
                    <!-- <option value="Другой документ">Другой документ</option> -->
                </select>
            </div>
            <div class="uk-margin-small-top">
                <label for="passport_passenger">Серия документа</label>
                <input class="uk-input" id="passport_passenger" type="text" name="passport_passenger" value="<?php echo $page_passenger->passport_passenger ?>" placeholder="Параметры документа" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top">
                <label for="num_doc_passenger">Номер документа</label>
                <input class="uk-input" id="num_doc_passenger" type="text" name="num_doc_passenger" value="<?php echo $page_passenger->num_doc_passenger ?>" placeholder="Серия и номер" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top">
                <label for="phone_passenger">Номер телефона</label>
                <input class="uk-input" id="phone_passenger" type="text" name="phone_passenger" value="<?php echo $page_passenger->phone_passenger ?>" placeholder="Телефон пассажира" autocomplete="off" required>
            </div>
            
            <br>
            <div class="uk-flex uk-flex-center">
                <button id="edit_passenger" class="uk-button uk-button-primary" type="button">Править</button>
            </div>
        </div>
    </div>
</div>
	
<?php   
}
?>