<?php namespace ProcessWire;

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
    	<h1 class="uk-heading-hero uk-text-center">Отчет по регистрации пассажиров</h1>      
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
$all_passengers = $pages->find('template=passengers, published>' . $start . ', published<' . $end . ', sort=published');
$arr_all_passengers = [];
foreach ($all_passengers as $all_passengers_item) {
    $arr_all_passengers[] = array(
        "name_passenger"=>$all_passengers_item->name_passenger,
        "phone_passenger"=>$all_passengers_item->phone_passenger
        );
}
//echo '<pre>'; print_r($arr_all_agent_tickets); echo '</pre>';

$reestr_all_passengers = '';
foreach ($arr_all_passengers as $key => $val) {
$reestr_all_passengers .= '
    <p class="reestr_seat_item">
    ФИО: ' . $val['name_passenger'] . '<br> 
    Телефон: ' . $val['phone_passenger'] . '<br> 
    </p>
';
}
?>

    <div id="content" style="max-width: 700px;">
        <h1 class="uk-heading-hero uk-text-center">Отчет по регистрации пассажиров</h1>
                    
        <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
            <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
            <h4 class="uk-margin-remove">Дата: с <span style="font-weight: 700;"><?php echo $start_date; ?></span> по <span style="font-weight: 700;"><?php echo $finish_date; ?></span></h4>
            
            <div class="uk-flex uk-flex-column">
                <br>
                <h3 class="uk-margin-remove uk-card-title">Информация о пассажирах</h3>
                <div class="reestr_seat uk-flex" style="max-height: 700px;">
                    <?php echo $reestr_all_passengers ; ?>
                </div>
                
                <form class="uk-flex uk-flex-column" id="print_informer_bus" action="/otchet-po-registratcii-passazhirov-pechat/" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="start_date" type="text" name="start_date" value="<?php echo $start_date ; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="finish_date" type="text" name="finish_date" value="<?php echo $finish_date ; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                    <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Скачать отчет</button>
                    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-registratcii-passazhirov-vybor-perioda/">Выбрать другого период</a>
                    <a class="uk-margin-small uk-button uk-button-default" href="/">Вернутся на главную</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
}
?>