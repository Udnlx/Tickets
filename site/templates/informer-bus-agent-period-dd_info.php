<?php namespace ProcessWire;

$selected_bus = !empty($_POST['post_bus'])?$_POST['post_bus']:NULL;  
$selected_id_bus = !empty($_POST['post_id_bus'])?$_POST['post_id_bus']:NULL;
$selected_time = !empty($_POST['post_time'])?$_POST['post_time']:NULL;

$agent = !empty($_POST['agent'])?$_POST['agent']:NULL;
$print_agent = !empty($_POST['agent'])?$_POST['agent']:NULL;
if ($agent == 'Олимп + API') {
    $agent = 'Олимп|Site|APP';
}

$start_date = !empty($_POST['start_date'])?$_POST['start_date']:NULL;
$finish_date = !empty($_POST['finish_date'])?$_POST['finish_date']:NULL;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Отчет по рейсу и агенту за период</h1>
    <h3 class="uk-margin-remove uk-card-title uk-text-center">По дате выезда</h3> 
	<br>       
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php
$all_agent_tickets = $pages->find('template=purchased_tickets, id_bus=' . $selected_id_bus . ', date_depart>=' . $start_date . ', date_depart<=' . $finish_date . ', agent_ticket=' . $agent . ', sort=date_depart');
$arr_all_agent_tickets = [];
foreach ($all_agent_tickets as $all_agent_tickets_item) {
        $arr_all_agent_tickets[] = array(
            "agent"=>$all_agent_tickets_item->agent_ticket,
            "bus"=>$all_agent_tickets_item->title,
            "pay_or_booking"=>$all_agent_tickets_item->pay_or_booking,
            "confirm"=>$all_agent_tickets_item->confirm,
            "price_ticket"=>$all_agent_tickets_item->price_ticket,
            "booking_sum"=>$all_agent_tickets_item->booking_sum,
            "passenger"=>$all_agent_tickets_item->passenger,
            "type_ticket"=>$all_agent_tickets_item->type_ticket,
            "passenger_doc"=>$all_agent_tickets_item->passenger_doc,
            "operator"=>$all_agent_tickets_item->operator,
            "reg_ticket"=>date("Y-m-d H:i:s", $all_agent_tickets_item->published) 
            );
    }
//echo '<pre>'; print_r($arr_reserv_seat); echo '</pre>';

$reestr_all_agent_tickets = '';
foreach ($arr_all_agent_tickets as $key => $val) {
    $reestr_all_agent_tickets .= '
        <p class="reestr_seat_item">
        Агент: ' . $val['agent'] . '<br> 
        Автобус: ' . $val['bus'] . '<br> 
        Стутус: ' . $val['pay_or_booking'] . ' - ' . $val['confirm'] . '<br>
        Цена билета: ' . $val['price_ticket'] . '<br>
        Сумма к оплате: ' . $val['booking_sum'] . '<br>
        Пассажир: ' . $val['passenger'] . ' - ' . $val['type_ticket'] . ' - ' . $val['passenger_doc'] . '<br>
        <span> - Регистратор: ' . $val['operator'] . '</span><br>
        <span> - Билет зарегистрирован: ' . $val['reg_ticket'] . '</span>
        </p>
    ';
    }
?>

<div id="content">
	<h1 class="uk-heading-hero uk-text-center">Отчет по рейсу и агенту за период</h1>
    <h3 class="uk-margin-remove uk-card-title uk-text-center">По дате выезда</h3> 
    <br>
	<div class="uk-child-width-1-2@m" uk-grid>
	    
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" uk-sticky>
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <h4 class="uk-margin-remove">Выбранный рейс:<br><span style="font-weight: 700;"><?php echo $selected_bus; ?></span></h4>
                <h4 class="uk-margin-remove">Выбранный агент:<br><span style="font-weight: 700;"><?php echo $print_agent; ?></span></h4>
                <h4 class="uk-margin-remove">Дата: с <span style="font-weight: 700;"><?php echo $start_date; ?></span> по <span style="font-weight: 700;"><?php echo $finish_date; ?></span></h4>
                <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-reisu-i-agentam-za-period-dd-vybor-avtobusa/">Выбрать другие параметры</a>
                <a class="uk-margin-small uk-button uk-button-default" href="/">Вернутся на главную</a>
            </div>
        </div>
        
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Все билеты агента за период</h3>
                <div class="reestr_seat uk-flex noselect" style="max-height: 700px;">
                    <?php echo $reestr_all_agent_tickets ; ?>
                </div>
                
                
                <form class="uk-flex uk-flex-column" id="" action="/otchet-po-reisu-i-agentam-za-period-dd-pechat/" method="post">
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
                        <input class="uk-input" id="print_agent" type="text" name="print_agent" value="<?php echo $agent ; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <label for="start_date">Дата с</label>
                        <input class="uk-input" id="print_start_date" type="date" name="print_start_date" value="<?php echo $start_date ; ?>">
                    </div>

                    <div class="uk-margin-small-top uk-hidden">
                        <label for="finish_date">Дата по</label>
                        <input class="uk-input" id="print_finish_date" type="date" name="print_finish_date" value="<?php echo $finish_date ; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                    <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Скачать отчет</button>
                    </div>
                </form>
                
            </div>
        </div>
        
    </div>
</div>





<?php   
}
?>