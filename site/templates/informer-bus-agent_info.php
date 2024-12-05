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
	<h1 class="uk-heading-hero uk-text-center">Отчет по рейсу, по агентам</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php
$reestr_seat = '<ul uk-accordion style="margin:0;">';
$all_agents = $pages->get('template=agents');
foreach ($all_agents->agent_items as $agent_itm) {
    $reserv_seat = $pages->find('template=purchased_tickets, id_bus=' . $selected_id_bus . ', agent_ticket=' . $agent_itm->agent . ', date_depart=' . $selected_date . ', sort=seat');
    $arr_reserv_seat = [];
    foreach ($reserv_seat as $reserv_seat_item) {
        $arr_reserv_seat[] = array(
            "seat"=>$reserv_seat_item->seat,
            "pay_or_booking"=>$reserv_seat_item->pay_or_booking,
            "confirm"=>$reserv_seat_item->confirm,
            "station"=>$reserv_seat_item->name_station,
            "id_passenger"=>$reserv_seat_item->id_passenger,
            "passenger"=>$reserv_seat_item->passenger,
            "type_ticket"=>$reserv_seat_item->type_ticket,
            "passenger_doc"=>$reserv_seat_item->passenger_doc,
            "operator"=>$reserv_seat_item->operator,
            "agent_ticket"=>$reserv_seat_item->agent_ticket,
            );
    }
    //echo '<pre>'; print_r($arr_reserv_seat); echo '</pre>';

    if (count($reserv_seat) > 0) {
        $reestr_seat .= '
        <li>
            <a class="uk-accordion-title" href="#"><h4 style="margin:0;">' . $agent_itm->agent . ', продано ' . count($reserv_seat) . ' мест</h4></a>
            <div class="uk-accordion-content" style="margin:0;">
        ';
        foreach ($arr_reserv_seat as $key => $val) {
        $data_passenger = $pages->get('template=passengers, id=' . $val['id_passenger'] . '');
        $phone_passenger = $data_passenger->phone_passenger;
        $reestr_seat .= '
            <p class="reestr_seat_item" style="margin: 10px 0 0 0;">Место - ' . $val['seat'] . ' - ' . $val['pay_or_booking'] . ' - ' . $val['confirm'] . '<br> Станция посадки: ' . $val['station'] . '<br>' . $val['passenger'] . '<br>тип билета: ' . $val['type_ticket'] . '<br>' . $val['passenger_doc'] . '<br>телефон: ' . $phone_passenger . '<br><span> - Регистратор: ' . $val['operator'] . '</span><br><span> - Агент: ' . $val['agent_ticket'] . '</span></p>
        ';
        }
        $reestr_seat .= '
            </div>
        </li>
        ';
    }
}
$reestr_seat .= '</ul>';
?>

<div id="content">
	<h1 class="uk-heading-hero uk-text-center">Отчет по рейсу, по агентам</h1>
	<div class="uk-child-width-1-2@m" uk-grid>
	    
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" uk-sticky>
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <h4 class="uk-margin-remove">Выбранный рейс:<br><span style="font-weight: 700;"><?php echo $selected_bus; ?></span></h4>
                <h4 class="uk-margin-remove">Дата: <span style="font-weight: 700;"><?php echo $selected_date; ?></span> отправление<span style="font-weight: 700;"><?php echo $selected_time; ?></span></h4>
                <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-reisu-agenty-vybor-avtobusa/">Выбрать другой рейс</a>
                <a class="uk-margin-small uk-button uk-button-default" href="/">Вернутся на главную</a>
            </div>
        </div>
        
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Реестр уже купленных мест по агентам</h3>
                <div class="reestr_seat uk-flex noselect" style="max-height: 700px;">
                    <?php echo $reestr_seat ; ?>
                </div>
                
                <!--
                <form class="uk-flex uk-flex-column" id="print_informer_bus" action="" method="post">
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
                -->
            </div>
        </div>
        
    </div>
</div>





<?php   
}
?>