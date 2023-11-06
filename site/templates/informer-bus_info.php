<?php namespace ProcessWire;

$selected_bus = !empty($_POST['post_bus'])?$_POST['post_bus']:NULL;  
$selected_id_bus = !empty($_POST['post_id_bus'])?$_POST['post_id_bus']:NULL;
$selected_date = !empty($_POST['post_date'])?$_POST['post_date']:NULL;
$selected_time = !empty($_POST['post_time'])?$_POST['post_time']:NULL;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Отчет по рейсу</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php
$reserv_seat = $pages->find('template=purchased_tickets, id_bus=' . $selected_id_bus . ', date_depart=' . $selected_date . ',sort=seat');
$arr_reserv_seat = [];
foreach ($reserv_seat as $reserv_seat_item) {
    $arr_reserv_seat[] = array(
        "seat"=>$reserv_seat_item->seat,
        "pay_or_booking"=>$reserv_seat_item->pay_or_booking,
        "confirm"=>$reserv_seat_item->confirm,
        "passenger"=>$reserv_seat_item->passenger,
        "passenger_doc"=>$reserv_seat_item->passenger_doc,
        "operator"=>$reserv_seat_item->operator
        );
}
//echo '<pre>'; print_r($arr_reserv_seat); echo '</pre>';

$reestr_seat = '';
foreach ($arr_reserv_seat as $key => $val) {
$reestr_seat .= '
    <p class="reestr_seat_item">Место - ' . $val['seat'] . ' - ' . $val['pay_or_booking'] . ' - ' . $val['confirm'] . ' - ' . $val['passenger'] . ' - ' . $val['passenger_doc'] . '<br><span> - Регистратор: ' . $val['operator'] . '</span></p>
';
}
?>

<div id="content">
	<h1 class="uk-heading-hero uk-text-center">Отчет по рейсу</h1>
	<div class="uk-child-width-1-2@m" uk-grid>
	    
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" uk-sticky>
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <h4 class="uk-margin-remove">Выбранный рейс:<br><span style="font-weight: 700;"><?php echo $selected_bus; ?></span></h4>
                <h4 class="uk-margin-remove">Дата: <span style="font-weight: 700;"><?php echo $selected_date; ?></span> отправление<span style="font-weight: 700;"><?php echo $selected_time; ?></span></h4>
                <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-reisu-vybor-avtobusa/">Выбрать другой рейс</a>
                <a class="uk-margin-small uk-button uk-button-default" href="/">Вернутся на главную</a>
            </div>
        </div>
        
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Реестр уже купленных мест</h3>
                <div class="reestr_seat uk-flex" style="max-height: 700px;">
                    <?php echo $reestr_seat ; ?>
                </div>
                
                <form class="uk-flex uk-flex-column" id="print_informer_bus" action="/otchet-po-reisu-pechat/" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input readonly" id="print_bus" type="text" name="print_bus" value="<?php echo $selected_bus ; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="print_id_bus" type="text" name="print_id_bus" value="<?php echo $selected_id_bus ; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="print_time" type="text" name="print_time" value="<?php echo $selected_time ; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="print_date" type="date" name="print_date" value="<?php echo $selected_date ; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                    <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Скачать отчет</button>
                    </div>
                </form>

                <form class="uk-flex uk-flex-column" id="print_select_seat" action="/otchet-po-reisu-dlia-voditelia/" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="print_selected_bus" type="text" name="print_selected_bus" value="<?php echo $selected_bus; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="print_selected_id_bus" type="text" name="print_selected_id_bus" value="<?php echo $selected_id_bus; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="print_selected_date" type="text" name="print_selected_date" value="<?php echo $selected_date; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="print_selected_time" type="text" name="print_selected_time" value="<?php echo $selected_time; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Распечатка для водителя</button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>





<?php   
}
?>