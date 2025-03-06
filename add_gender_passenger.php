<?php

namespace ProcessWire;

require_once 'index.php';

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}





$id_passenger = $_POST['id_passenger'];
$gender_passenger = $_POST['gender_passenger'];

if ($id_passenger == '' || $gender_passenger == '') {
    echo '<p class="messages" style="color: red;">Ошибка. Пол не добавлен.<br>Укажите пол и повторите попытку.</p>';    
} else {
    $passenger = '';
    
    echo '<p class="messages" style="color: green;">Пол добавлен успешно</p>';
    
    $edit_page = $pages->get('template=passengers, id=' . $id_passenger . '');
    $edit_page->of(false);
    $edit_page->gender_passenger = $gender_passenger;
    $edit_page->save();
        
}