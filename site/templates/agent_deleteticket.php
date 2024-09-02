<?php namespace ProcessWire;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

$selected_bus = !empty($_POST['del_selected_bus'])?$_POST['del_selected_bus']:NULL;  
$selected_id_bus = !empty($_POST['del_selected_id_bus'])?$_POST['del_selected_id_bus']:NULL;
$selected_date = !empty($_POST['del_selected_date'])?$_POST['del_selected_date']:NULL;
$selected_time = !empty($_POST['del_selected_time'])?$_POST['del_selected_time']:NULL;

$selected_seat = !empty($_POST['del_selected_seat'])?$_POST['del_selected_seat']:NULL;
$id_seat = !empty($_POST['del_id_seat'])?$_POST['del_id_seat']:NULL;
$passenger = !empty($_POST['del_passenger'])?$_POST['del_passenger']:NULL;

$success = 'Билет успешно удален';
$log = '';
if ($selected_bus && $selected_id_bus && $selected_date && $selected_time && $selected_seat && $id_seat && $passenger) {
    $log .= date('Y-m-d H:i:s') . ' - Удален билет id - ' . $id_seat . '. ';
    $log .= 'Билет удален оператором ' . $operator . '. '; 
    $log .= 'Параметры удаленного билета: ' . $selected_bus . ' ' . $selected_date . '' . $selected_time . ', id автобуса - ' . $selected_id_bus . ', место - ' . $selected_seat . ', пассажир - ' . $passenger; 
    file_put_contents(__DIR__ . '/log_agent_delete_tikets.txt', $log . PHP_EOL, FILE_APPEND);
    
    $delete_page = $pages->get('template=purchased_tickets, id=' . $id_seat . '');
    $delete_page->delete();
} else {
    $success = 'Билет не удален!<br>Ошибка в данных';
}



if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Билет не удален</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center"><?php echo $success; ?></h1>
	
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
        <h4 class="uk-margin-remove">Данные о билете:</h4>
        <p class="uk-margin-remove">Автобус: <span style="font-weight: 700;"><?php echo $selected_bus; ?></span></p>
        <p class="uk-margin-remove">ID автобуса: <span style="font-weight: 700;"><?php echo $selected_id_bus; ?></span></p>
        <p class="uk-margin-remove">Дата и время отправления: <span style="font-weight: 700;"><?php echo $selected_date; ?> <?php echo $selected_time; ?></span></p>
        <p class="uk-margin-remove">Место: <span class="uk-text-success" style="font-weight: 700;"><?php echo $selected_seat; ?> освобожденно</span></p>
        <p class="uk-margin-remove">ID билета: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $id_seat; ?> больше не существует</span></p>
        <p class="uk-margin-remove">Пассажир: <span class="uk-text-danger" style="font-weight: 700;">Бронь и оплата пассажиром <?php echo $passenger; ?> на это место в системе аннулирована</span></p>

        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>
	
<?php   
}
?>