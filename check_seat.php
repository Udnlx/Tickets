<?php

namespace ProcessWire;

require_once 'index.php';





$selected_bus = $_POST['check_selected_bus'];
$selected_id_bus = $_POST['check_selected_id_bus'];
$selected_date = $_POST['check_selected_date'];
$selected_time = $_POST['check_selected_time'];
$selected_seat = $_POST['check_selected_seat'];

$select_seat = $pages->find('template=purchased_tickets, id_bus=' . $selected_id_bus . ', date_depart=' . $selected_date . ', seat=' . $selected_seat . '');

if ($select_seat != '') {
    echo '<p class="messages" style="color: red;">Ошибка. Место ' . $selected_seat . ' уже заполненно другим оператором<br>Выберите пожалуйста другое</p>';
} else {
    echo '<p class="messages" style="color: green;">Место свободно, регистрируем</p>';
}