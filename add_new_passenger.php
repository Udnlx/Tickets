<?php

namespace ProcessWire;

require_once 'index.php';

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}





$add_name_passenger = $_POST['add_name_passenger'];
$add_gender_passenger = $_POST['add_gender_passenger'];
$add_citizenship_passenger = $_POST['add_citizenship_passenger'];
$add_birthday_passenger = $_POST['add_birthday_passenger'];
$add_type_doc_passenger = $_POST['add_type_doc_passenger'];
$add_num_doc_passenger = $_POST['add_num_doc_passenger'];
$add_passport_passenger = $_POST['add_passport_passenger'];
$add_phone_passenger = $_POST['add_phone_passenger'];
$add_agent = $_POST['add_agent'];

if ($add_name_passenger == '' || $add_gender_passenger == '' || $add_citizenship_passenger == '' || $add_birthday_passenger == '' || $add_type_doc_passenger == '' || $add_num_doc_passenger == '' || $add_passport_passenger == '' || $add_phone_passenger == '') {
    echo '<p class="messages" style="color: red;">Ошибка. Пассажир не добавлен.<br>Проверьте заполненность полей и повторите попытку.</p>';    
} else {
    $passengers = '';
    $passengers = $pages->find('template=passengers, num_doc_passenger=' . $add_num_doc_passenger . '');
    if ($passengers != '') {
        echo '<p class="messages" style="color: red;">Ошибка. Пассажир не добавлен.<br>Пассажир с таким номером документа уже существует:</p>';
        foreach($passengers as $passenger) {
           echo '<p class="messages" style="color: green;">' . $passenger->name_passenger . '</p>';
        }
    } else {
        echo '<p class="messages" style="color: green;">Новый пассажир добавлен</p>';
        //echo $add_name_passenger . ' - ' . $add_gender_passenger . ' - ' . $add_birthday_passenger . ' - ' . $add_type_doc_passenger . ' - ' . $add_num_doc_passenger . ' - ' . $add_passport_passenger . ' - ' . $add_phone_passenger;
        
        $pages->add('passengers', '/passazhiry/', [
            'title' => $add_name_passenger,
            'name_passenger' => $add_name_passenger,
            'gender_passenger' => $add_gender_passenger,
            'citizenship_passenger' => $add_citizenship_passenger,
            'birthday_passenger' => $add_birthday_passenger,
            'type_doc_passenger' => $add_type_doc_passenger,
            'num_doc_passenger' => $add_num_doc_passenger,
            'passport_passenger' => $add_passport_passenger,
            'phone_passenger' => $add_phone_passenger,
            'agent' => $add_agent,
        ]);
        $passenger_page = $pages->get('template=passengers, num_doc_passenger=' . $add_num_doc_passenger . '');
        $passenger_page_id = $passenger_page->id;
        $log = '';
        $log .= date('Y-m-d H:i:s') . ' - Добавлен новый пассажир id - ' . $passenger_page_id . ' оператором ' . $operator . '.   ';
        $log .= 'Данные добавленного пассажира: ' . $add_name_passenger . ' - ' . $add_gender_passenger . ' - ' . $add_citizenship_passenger . ' - ' . $add_birthday_passenger . ' - ' . $add_type_doc_passenger . ' - ' . $add_num_doc_passenger . ' - ' . $add_passport_passenger . ' - ' . $add_phone_passenger;
        file_put_contents(__DIR__ . '/site/templates/log_add_passengers.txt', $log . PHP_EOL, FILE_APPEND);
        
        $all_passengers = $pages->find('template=passengers, title~*=' . $add_name_passenger . '');
        $arr_all_passengers = [];
        foreach ($all_passengers as $all_passengers_item) {
            $arr_all_passengers[] = array(
                "id_passenger"=>$all_passengers_item->id,
                "name_passenger"=>$all_passengers_item->name_passenger,
                "gender_passenger"=>$all_passengers_item->gender_passenger,
                "birthday_passenger"=>$all_passengers_item->birthday_passenger,
                "type_doc_passenger"=>$all_passengers_item->type_doc_passenger,
                "num_doc_passenger"=>$all_passengers_item->num_doc_passenger,
                "passport_passenger"=>$all_passengers_item->passport_passenger,
                "phone_passenger"=>$all_passengers_item->phone_passenger,
                "count_travel"=>$all_passengers_item->count_travel
                );
        }
        $passengers = '';
        foreach ($arr_all_passengers as $key => $val) {
        $passengers .= '
        <p id="' . $val['id_passenger'] . '" count_travel="' . $val['count_travel'] . '" class="passengers_item">' . $val['name_passenger'] . '<br><span>' . $val['gender_passenger'] . ' — ' . $val['birthday_passenger'] . ' — ' . $val['type_doc_passenger'] . ' — ' . $val['passport_passenger'] . ' — ' . $val['num_doc_passenger'] . '<br>' . $val['phone_passenger'] . '</span><br><span>Всего поездок: ' . $val['count_travel'] . '</span></p>
        ';
        }
        echo '
            <div id="get_all_passengers" style="display: none">
                '. $passengers .'
            </div>
        ';
    }
}