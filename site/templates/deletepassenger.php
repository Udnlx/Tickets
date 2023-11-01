<?php namespace ProcessWire;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

$passenger_idpassenger = !empty($_POST['passenger_idpassenger'])?$_POST['passenger_idpassenger']:NULL;  
$passenger_name = !empty($_POST['passenger_name'])?$_POST['passenger_name']:NULL;  
$passenger_document = !empty($_POST['passenger_document'])?$_POST['passenger_document']:NULL;  

$success = 'Пассажир успешно удален';
$log = '';
if ($passenger_idpassenger && $passenger_name && $passenger_document) {
    $log .= date('Y-m-d H:i:s') . ' - Удален пассажир id - ' . $passenger_idpassenger . '. ';
    $log .= 'Пассажир удален оператором ' . $operator . '. '; 
    $log .= 'Параметры удаленного пассажира: ' . $passenger_idpassenger . ' - ' . $passenger_name . ' - ' . $passenger_document; 
    file_put_contents(__DIR__ . '/log_delete_passengers.txt', $log . PHP_EOL, FILE_APPEND);
    
    $delete_page = $pages->get('template=passengers, id=' . $passenger_idpassenger . '');
    $delete_page->status = $delete_page->status | Page::statusUnpublished; 
    $delete_page->save();
} else {
    $success = 'Пассажир не удален!<br>Ошибка в данных';
}



if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Пассажир не удален</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сесия потеряна, перезайти</h3>
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
        <h4 class="uk-margin-remove">Данные об удаленном пассажире:</h4>
        <p class="uk-margin-remove">ID пассажира: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $passenger_idpassenger; ?></span></p>
        <p class="uk-margin-remove">ФИО пассажира: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $passenger_name; ?></span></p>
        <p class="uk-margin-remove">Документ пассажира: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $passenger_document; ?></span></p>

        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>
	
<?php   
}
?>