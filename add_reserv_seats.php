<?php

namespace ProcessWire;

require_once 'index.php';





$selected_bus = $_POST['selected_bus'];
$selected_id_bus = $_POST['selected_id_bus'];
$selected_date = $_POST['selected_date'];
$selected_time = $_POST['selected_time'];
$select_reserv_seat = $_POST['select_reserv_seat'];

if ($selected_bus == '' || $selected_id_bus == '' || $selected_date == '' || $selected_time == '' || $select_reserv_seat == '') {
    echo '<p class="messages" style="color: red;">Ошибка. Изменения не внесены.<br>Ошибка в данных.</p>';    
} else {
    $reserv_seats = '';
    $reserv_seats = $pages->get('template=reserv_seats, title=' . $selected_bus . ' - ' . $selected_date . '');
    if ($reserv_seats != '') {

        $edit_page = $pages->get('template=reserv_seats, id=' . $reserv_seats->id . '');
        $edit_page->of(false);
        $edit_page->mass_reserv_seats = $select_reserv_seat;
        $edit_page->save();

        echo '<p class="messages" style="color: green;">
                Запись на резервирование мест<br>' . $selected_bus . ' - ' . $selected_date . ', id=' . $reserv_seats->id . '<br>успешно отредактирована
            </p>';
    } else {

        $pages->add('reserv_seats', '/rezerv-biletov/', [
            'title' => $selected_bus . ' - ' . $selected_date,
            'bus' => $selected_bus,
            'id_bus' => $selected_id_bus,
            'date_depart' => $selected_date,
            'mass_reserv_seats' => $select_reserv_seat,
        ]);

        echo '<p class="messages" style="color: green;">
                Запись на резервирование мест<br>' . $selected_bus . ' - ' . $selected_date . '<br>успешно создана
            </p>';
    }
}