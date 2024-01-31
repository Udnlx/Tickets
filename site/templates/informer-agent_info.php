<?php namespace ProcessWire;

$agent = !empty($_POST['agent'])?$_POST['agent']:NULL;  
$start_date = !empty($_POST['start_date'])?$_POST['start_date']:NULL;
$finish_date = !empty($_POST['finish_date'])?$_POST['finish_date']:NULL;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

$access = '';
if(isset($_SESSION['access'])){
    $access = $_SESSION['access'];
}

if ($operator == 'no_operator') {
?>
    <div id="content" style="max-width: 700px;">
    	<h1 class="uk-heading-hero uk-text-center">Отчет по агенту</h1>      
        <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
            <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
            <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
        </div>
    </div>
<?php    
} else {
?>

<?php
$start = strtotime( date($start_date) . " 00:00:00");
$end = strtotime( date($finish_date) . " 23:59:59");
$all_agent_tickets = $pages->find('template=purchased_tickets, published>' . $start . ', published<' . $end . ', agent_ticket=' . $agent . '');
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
//echo '<pre>'; print_r($arr_all_agent_tickets); echo '</pre>';

// $reestr_all_agent_tickets = '';
// foreach ($arr_all_agent_tickets as $key => $val) {
// $reestr_all_agent_tickets .= '
//     <p class="reestr_seat_item">
//     Агент: ' . $val['agent'] . '<br> 
//     Автобус: ' . $val['bus'] . '<br> 
//     Стутус: ' . $val['pay_or_booking'] . ' - ' . $val['confirm'] . '<br>
//     Цена билета: ' . $val['price_ticket'] . '<br>
//     Сумма к оплате: ' . $val['booking_sum'] . '<br>
//     Пассажир: ' . $val['passenger'] . ' - ' . $val['type_ticket'] . ' - ' . $val['passenger_doc'] . '<br>
//     <span> - Регистратор: ' . $val['operator'] . '</span><br>
//     <span> - Билет зарегистрирован: ' . $val['reg_ticket'] . '</span>
//     </p>
// ';
// }
?>

    <div id="content" style="max-width: 700px;">
        <h1 class="uk-heading-hero uk-text-center">Отчет по агенту</h1>
                    
        <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
            <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
            <h4 class="uk-margin-remove">Выбранный агент: <span style="font-weight: 700;"><?php echo $agent; ?></span></h4>
            <h4 class="uk-margin-remove">Дата: с <span style="font-weight: 700;"><?php echo $start_date; ?></span> по <span style="font-weight: 700;"><?php echo $finish_date; ?></span></h4>
            
            <div class="uk-flex uk-flex-column">
                <br>
                <h3 class="uk-margin-remove uk-card-title">Информация о билетах</h3>
                <div class="reestr_seat uk-flex" style="max-height: 700px;">
                    <?php //echo $reestr_all_agent_tickets ; ?>
                </div>
                
                <form class="uk-flex uk-flex-column" id="print_informer_bus" action="" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="agent" type="text" name="agent" value="<?php echo $agent ; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="start_date" type="text" name="start_date" value="<?php echo $start_date ; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="finish_date" type="text" name="finish_date" value="<?php echo $finish_date ; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                    <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Скачать отчет (НЕ НАЖИМАТЬ)</button>
                    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-agentu-vybor-agenta/">Выбрать другого агента</a>
                    <a class="uk-margin-small uk-button uk-button-default" href="/">Вернутся на главную</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
}
?>