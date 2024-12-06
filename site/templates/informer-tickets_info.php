<?php namespace ProcessWire;

$user = !empty($_POST['user'])?$_POST['user']:NULL;  
$start_date = !empty($_POST['start_date'])?$_POST['start_date']:NULL;
$finish_date = !empty($_POST['finish_date'])?$_POST['finish_date']:NULL;

$s_date = strtotime($start_date);
$f_date = strtotime($finish_date . ' +1 day');

$filter_user = '';
if ($user == 'Все') {
    $filter_user = '';
} else {
    $filter_user = 'operator=' . $user . ',';
}

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
    	<h1 class="uk-heading-hero uk-text-center">Отчет по билетам</h1>      
        <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
            <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
            <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
        </div>
    </div>
<?php    
} else {
?>

<?php
$reestr_all_user_tickets = '';

$all_user_tickets = $pages->find('template=purchased_tickets, ' . $filter_user . ' created>=' . $s_date . ', created<=' . $f_date . ', sort=created');
$arr_all_user_tickets = [];
foreach ($all_user_tickets as $all_user_tickets_item) {
    if ($all_user_tickets_item->agent_ticket != 'Автовокзал') {
        $arr_all_user_tickets[] = array(
        "reg_ticket"=>date("Y-m-d H:i:s", $all_user_tickets_item->published), 
        "operator"=>$all_user_tickets_item->operator,
        "bus"=>$all_user_tickets_item->title,
        "agent"=>$all_user_tickets_item->agent_ticket,
        );
    }
}
//echo '<pre>'; print_r($arr_all_user_tickets); echo '</pre>';

foreach ($arr_all_user_tickets as $key => $val) {
    $reestr_all_user_tickets .= '
        <p class="reestr_seat_item">
        Дата регистрации: ' . $val['reg_ticket'] . '<br> 
        Оператор: ' . $val['operator'] . '<br>
        Автобус: ' . $val['bus'] . '<br>
        Агент: ' . $val['agent'] . '
        </p>
    ';
}

if ($reestr_all_user_tickets == '') {
    $reestr_all_user_tickets .= '
    Записей не найдено, попробуйте задать другие параметры для поиска
    ';
}
?>

    <div id="content" style="max-width: 700px;">
        <h1 class="uk-heading-hero uk-text-center">Отчет по билетам</h1>
                    
        <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
            <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
            <h4 class="uk-margin-remove">Выбранный оператор: <span style="font-weight: 700;"><?php echo $user; ?></span></h4>
            <h4 class="uk-margin-remove">Дата: с <span style="font-weight: 700;"><?php echo $start_date; ?></span> по <span style="font-weight: 700;"><?php echo $finish_date; ?></span></h4>
            
            <div class="uk-flex uk-flex-column">
                <br>
                <h3 class="uk-margin-remove uk-card-title">Информация о билетах</h3>
                <div class="reestr_seat uk-flex noselect" style="max-height: 700px;">
                    <?php echo $reestr_all_user_tickets ; ?>
                </div>
                
                <form class="uk-flex uk-flex-column" id="print_informer_bus" action="/otchet-po-biletam-pechat/" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="user" type="text" name="user" value="<?php echo $user ; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="start_date" type="text" name="start_date" value="<?php echo $start_date ; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="finish_date" type="text" name="finish_date" value="<?php echo $finish_date ; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                    <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Скачать отчет</button>
                    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-biletam-vybor-parametrov/">Выбрать другие параметры</a>
                    <a class="uk-margin-small uk-button uk-button-default" href="/">Вернутся на главную</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
}
?>