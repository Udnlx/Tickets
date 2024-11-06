<?php

namespace ProcessWire;

require_once 'index.php';





$selected_bus = $_POST['check_selected_bus'];
$selected_id_bus = $_POST['check_selected_id_bus'];
$selected_date = $_POST['check_selected_date'];
$selected_time = $_POST['check_selected_time'];
$selected_seat = $_POST['check_selected_seat'];
$selected_name = $_POST['check_selected_name'];

$select_seat = $pages->find('template=purchased_tickets, id_bus=' . $selected_id_bus . ', date_depart=' . $selected_date . ', seat=' . $selected_seat . '');
$select_name = $pages->get('template=purchased_tickets, id_bus=' . $selected_id_bus . ', date_depart=' . $selected_date . ', passenger~=' . $selected_name . '');

if ($select_seat != '') {
    echo '<p class="messages" style="color: red;">Ошибка. Место ' . $selected_seat . ' уже заполненно другим оператором<br>Выберите пожалуйста другое</p>';
} else {
    if ($select_name != '') {
        echo '
        <p class="warning" style="color: orange;">Внимание! Пассажир ' . $selected_name . ' уже присутствует в текущем автобусе, занимает место ' . $select_name->seat . '</p>
        <p class="messages" style="color: green;">Проверка места пройдена, найден дубликат пассажира</p>
        ';
    } else {
        echo '<p class="messages" style="color: green;">Проверка места пройдена, регистрируем</p>';
    }
}