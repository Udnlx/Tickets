<?php namespace ProcessWire;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Выбор пассажира</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
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
	<h1 class="uk-heading-hero uk-text-center">Выбор пассажира</h1>
	<div class="uk-child-width-1-2@m" uk-grid>
	    
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" uk-sticky>
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <h3 class="uk-margin-remove uk-card-title">Выбранный пассажир</h3>
                <form class="uk-flex uk-flex-column" id="select_passenger" action="/reestr-passazhirov-dannye-passazhira/" method="post">
                    <div class="uk-margin-small-top  uk-hidden">
                        <input class="uk-input readonly" id="passenger_idpassenger" type="text" name="passenger_idpassenger" value="" placeholder="ID пассажира" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="passenger_name" type="text" name="passenger_name" value="" placeholder="ФИО пассажира" autocomplete="off" required>
                    </div>
                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="passenger_document" type="text" name="passenger_document" value="" placeholder="Документ пассажира" autocomplete="off" required>
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Редактировать пассажира</button>
                        <a class="uk-margin-small uk-button uk-button-default" href="/">Назад</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Реестр пассажиров</h3>
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