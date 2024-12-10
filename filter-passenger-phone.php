<?php

namespace ProcessWire;

require_once 'index.php';





$search_passenger = $_POST['search_passenger'];

if ($search_passenger == '') {
    echo '<p class="messages" style="color: red;">Ошибка. Пустое поле для фильтрации.</p>';    
} else {
    $all_passengers = $pages->find('template=passengers, phone_passenger%=' . $search_passenger . '');
    $arr_all_passengers = [];
    foreach ($all_passengers as $all_passengers_item) {
        $arr_all_passengers[] = array(
            "id_passenger"=>$all_passengers_item->id,
            "name_passenger"=>$all_passengers_item->name_passenger,
            "birthday_passenger"=>$all_passengers_item->birthday_passenger,
            "type_doc_passenger"=>$all_passengers_item->type_doc_passenger,
            "num_doc_passenger"=>$all_passengers_item->num_doc_passenger,
            "passport_passenger"=>$all_passengers_item->passport_passenger,
            "phone_passenger"=>$all_passengers_item->phone_passenger,
            "count_travel"=>$all_passengers_item->count_travel
            );
    }
    //echo '<pre>'; print_r($arr_all_passengers); echo '</pre>';

    $passengers = '';
    foreach ($arr_all_passengers as $key => $val) {
    $passengers .= '
        <p id="' . $val['id_passenger'] . '" count_travel="' . $val['count_travel'] . '" class="passengers_item">' . $val['name_passenger'] . '<br><span>' . $val['birthday_passenger'] . ' — ' . $val['type_doc_passenger'] . ' — ' . $val['passport_passenger'] . ' — ' . $val['num_doc_passenger'] . '<br>' . $val['phone_passenger'] . '</span><br><span>Всего поездок: ' . $val['count_travel'] . '</span></p>
    ';
    }

    if ($passengers == '') {
        echo '<p class="messages" style="color: red;">Ничего не найдено, попробуйте поменять параметры и повторить фильтрацию.</p>';
    } else {
        echo $passengers;
    }
}