<?php

namespace ProcessWire;

require_once 'index.php';





$search_passenger = $_POST['search_passenger'];

if ($search_passenger == '') {
    echo '<p class="messages" style="color: red;">Ошибка. Пустое поле для фильтрации.</p>';    
} else {
    $all_passengers = $pages->find('template=passengers, num_doc_passenger%=' . $search_passenger . '');
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
    $lite_pp = mb_substr($val['passport_passenger'], -2, 2);
    $lite_ndp = mb_substr($val['num_doc_passenger'], -2, 2);
    $lite_phone = mb_substr($val['phone_passenger'], -5, 5);
    $passengers .= '
        <p id="' . $val['id_passenger'] . '" class="passengers_item">' . $val['name_passenger'] . '<br><span class="uk-hidden">' . $val['birthday_passenger'] . ' — ' . $val['type_doc_passenger'] . ' — ' . $val['passport_passenger'] . ' — ' . $val['num_doc_passenger'] . '<br>' . $val['phone_passenger'] . '</span></p>
        <p style="margin: -4px 0 4px 0 !important;font-size: 11px;"><span>' . $val['birthday_passenger'] . ', ' . $val['type_doc_passenger'] . ', ...' . $lite_pp . ', ......' . $lite_ndp . '<br>.........' . $lite_phone . '</span></p>
    ';
    }

    if ($passengers == '') {
        echo '<p class="messages" style="color: red;">Ничего не найдено, попробуйте поменять параметры и повторить фильтрацию.</p>';
    } else {
        echo $passengers;
    }
}