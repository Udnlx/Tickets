<?php

namespace ProcessWire;

require_once 'index.php';

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}





$id_passenger = $_POST['id_passenger'];
$edit_name_passenger = $_POST['edit_name_passenger'];
$edit_birthday_passenger = $_POST['edit_birthday_passenger'];
$edit_type_doc_passenger = $_POST['edit_type_doc_passenger'];
$edit_num_doc_passenger = $_POST['edit_num_doc_passenger'];
$edit_passport_passenger = $_POST['edit_passport_passenger'];
$edit_phone_passenger = $_POST['edit_phone_passenger'];

$old_birthday_passenger = $_POST['old_birthday_passenger'];
$old_type_doc_passenger = $_POST['old_type_doc_passenger'];
$old_num_doc_passenger = $_POST['old_num_doc_passenger'];
$old_passport_passenger = $_POST['old_passport_passenger'];
$old_phone_passenger = $_POST['old_phone_passenger'];

if ($id_passenger == '' || $edit_name_passenger == '' || $edit_birthday_passenger == '' || $edit_type_doc_passenger == '' || $edit_num_doc_passenger == '' || $edit_passport_passenger == '' || $edit_phone_passenger == '') {
    echo '<p class="messages" style="color: red;">Ошибка. Правки не внесены.<br>Проверьте заполненность полей и повторите попытку.</p>';    
} else {
    $passenger = '';
    $passenger = $pages->get('template=passengers, id=' . $id_passenger . '');
    
    $log .= date('Y-m-d H:i:s') . ' - Изменены данные пассажира id - ' . $id_passenger . ' оператором ' . $operator . '.   ';
    $log .= 'Старые значения: ' . $old_birthday_passenger . ' - ' . $old_type_doc_passenger . ' - ' . $old_num_doc_passenger . ' - ' . $old_passport_passenger . ' - ' . $old_phone_passenger . '   ';
    $log .= 'Новые значения: ' . $edit_birthday_passenger . ' - ' . $edit_type_doc_passenger . ' - ' . $edit_num_doc_passenger . ' - ' . $edit_passport_passenger . ' - ' . $edit_phone_passenger;
    file_put_contents(__DIR__ . '/site/templates/log_edit_passengers.txt', $log . PHP_EOL, FILE_APPEND);
    
    echo '<p class="messages" style="color: green;">Правки внесены успешно,<br>сейчас страница пассажира будет перезагружена</p>';
    //echo $edit_name_passenger . ' - ' . $edit_birthday_passenger . ' - ' . $edit_type_doc_passenger . ' - ' . $edit_num_doc_passenger . ' - ' . $edit_passport_passenger . ' - ' . $edit_phone_passenger;
    
    $edit_page = $pages->get('template=passengers, id=' . $id_passenger . '');
    $edit_page->of(false);
    $edit_page->birthday_passenger = $edit_birthday_passenger;
    $edit_page->type_doc_passenger = $edit_type_doc_passenger;
    $edit_page->num_doc_passenger = $edit_num_doc_passenger;
    $edit_page->passport_passenger = $edit_passport_passenger;
    $edit_page->phone_passenger = $edit_phone_passenger;
    $edit_page->save();
        
    }