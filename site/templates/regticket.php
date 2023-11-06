<?php namespace ProcessWire;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

$selected_bus = !empty($_POST['selected_bus'])?$_POST['selected_bus']:NULL;  
$selected_id_bus = !empty($_POST['selected_id_bus'])?$_POST['selected_id_bus']:NULL;
$selected_date = !empty($_POST['selected_date'])?$_POST['selected_date']:NULL;
$selected_time = !empty($_POST['selected_time'])?$_POST['selected_time']:NULL;

$selected_seat = !empty($_POST['selected_seat'])?$_POST['selected_seat']:NULL;
$pay_or_booking = !empty($_POST['pay_or_booking'])?$_POST['pay_or_booking']:NULL;
$confirm = !empty($_POST['confirm'])?$_POST['confirm']:NULL;
$selected_idpassenger = !empty($_POST['selected_idpassenger'])?$_POST['selected_idpassenger']:NULL;
$selected_name = !empty($_POST['selected_name'])?$_POST['selected_name']:NULL;
$selected_document = !empty($_POST['selected_document'])?$_POST['selected_document']:NULL;

$success = 'Билет успешно зарегистрирован';
if ($selected_bus && $selected_id_bus && $selected_date && $selected_time && $selected_seat && $selected_name && $selected_document) {
    //echo $selected_bus . $selected_id_bus . $selected_date . $selected_time . $selected_seat . $selected_name . $selected_document . $selected_idpassenger;
    $pages->add('purchased_tickets', 1026 , [
    'title' => $selected_bus . ' - ' . $selected_date . ' ' . $selected_time . ' место-' . $selected_seat,
    'bus' => $selected_bus,
    'id_bus' => $selected_id_bus,
    'date_depart' => $selected_date,
    'time_depart' => $selected_time,
    'seat' => $selected_seat,
    'pay_or_booking' => $pay_or_booking,
    'confirm' => $confirm,
    'id_passenger' => $selected_idpassenger,
    'passenger' => $selected_name,
    'passenger_doc' => $selected_document,
    'operator' => $operator,
    ]);
} else {
    $success = 'Билет не зарегистрирован!<br>Ошибка в данных';
}



if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Билет не зарегистрирован</h1>
	
	            
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
        <h4 class="uk-margin-remove">Данные о билете:</h4>
        <p class="uk-margin-remove">Автобус: <span style="font-weight: 700;"><?php echo $selected_bus; ?></span></p>
        <p class="uk-margin-remove">ID автобуса: <span style="font-weight: 700;"><?php echo $selected_id_bus; ?></span></p>
        <p class="uk-margin-remove">Дата и время отправления: <span style="font-weight: 700;"><?php echo $selected_date; ?> <?php echo $selected_time; ?></span></p>
        <p class="uk-margin-remove">Место: <span style="font-weight: 700;"><?php echo $selected_seat; ?></span></p>
        <p class="uk-margin-remove">Статус: <span style="font-weight: 700;"><?php echo $pay_or_booking; ?></span></p>
        <p class="uk-margin-remove">Статус подтверждения: <span style="font-weight: 700;"><?php echo $confirm; ?></span></p>
        <p class="uk-margin-remove">ФИО пассажира: <span style="font-weight: 700;"><?php echo $selected_name; ?></span></p>
        <p class="uk-margin-remove">Документ пассажира: <span style="font-weight: 700;"><?php echo $selected_document; ?></span></p>

        <form class="uk-flex uk-flex-column" id="select_bus" action="/registratciia-bileta-vybor-mesta/" method="post">
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="post_bus" type="text" name="post_bus" value="<?php echo $selected_bus ; ?>">
            </div>
            
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="post_id_bus" type="text" name="post_id_bus" value="<?php echo $selected_id_bus ; ?>">
            </div>
            
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="post_time" type="text" name="post_time" value="<?php echo $selected_time ; ?>">
            </div>
            
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="post_date" type="date" name="post_date" value="<?php echo $selected_date ; ?>">
            </div>
            
            <div class="uk-margin-small-top uk-flex uk-flex-column">
            <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Оформить еще один билет на этот же рейс</button>
            </div>
        </form>

        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>
	
<?php   
}
?>