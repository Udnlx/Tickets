<?php

$access = '';
if(isset($_SESSION['access'])){
    $access = $_SESSION['access'];
}

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

$sb_idbus = $_POST['sb_idbus'];
$sb_del_ticket = !empty($_POST['sb_del_ticket'])?$_POST['sb_del_ticket']:NULL;
$ticket_page = $pages->get('template=purchased_tickets, id=' . $sb_del_ticket . '');
if ($ticket_page->sb_ticket_id) {
    $run_operation = 'on';
} else {
    $run_operation = 'off';
}

$success = 'Билет успешно удален из 1С';
$log = '';
if ($sb_del_ticket && $run_operation == 'on') {
    $log .= date('Y-m-d H:i:s') . ' - Удален билет из 1С ID - ' . $ticket_page->sb_ticket_id . '. ';
    $log .= 'Билет удален оператором ' . $operator . '. '; 
    $log .= 'Параметры удаленного билета: ' . $ticket_page->title . '; ID - ' . $ticket_page->id; 
    file_put_contents(__DIR__ . '/log_agent_1c_ticket_delete.txt', $log . PHP_EOL, FILE_APPEND);
    
    //Удаляем билет из 1С
    //Подключаемся
    try{
        $param = array(
        'login' => 'atp5027241683-web',
        'password' => 'atp5027241683022020web0924',
        'trace' => true,
        'cache_wsdl' => 0,
        'encoding' => 'utf-8',
        'location' => 'http://cluster.avtovokzal.ru/gds114/soap/json',
        );
        $client = new SoapClient('http://cluster.avtovokzal.ru/gds114/soap/json?wsdl', $param);
    }
    catch (SoapFault $soapFault){
        echo '<h2>Не подключились к 1C</h2>';
        echo '<pre>'; 
        var_dump($soapFault);
        echo '</pre>';
    }
    //Подключаемся

    //Выполняем функцию
    try{
        $dataList = $client->returnTicket(["ticketId"=>'' . $ticket_page->sb_ticket_id . '']);
    }
    catch (SoapFault $soapFault){
        echo '<h2>Не удалось вызвать функцию</h2>';
        echo '<pre>'; 
        var_dump($soapFault);
        echo '</pre>';
    }
    //Выполняем функцию

    $ticket_page->of(false);
    $ticket_page->sb_ticket_id = '';
    $ticket_page->save();
    //Удаляем билет из 1С
} else {
    $success = 'Билет не удален из 1С!<br>Ошибка в данных';
    if ($run_operation == 'off') {
        $success = 'Билет не проведен в 1С!<br>Удаление не возможно';
    }
}



if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Билет не удален из 1С</h1>
	            
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
        <p class="uk-margin-remove">Автобус: <span style="font-weight: 700;"><?php echo $ticket_page->bus; ?></span></p>
        <p class="uk-margin-remove">ID автобуса: <span style="font-weight: 700;"><?php echo $ticket_page->id_bus; ?></span></p>
        <p class="uk-margin-remove">Дата и время отправления: <span style="font-weight: 700;"><?php echo $ticket_page->date_depart; ?> <?php echo $ticket_page->time_depart; ?></span></p>
        <p class="uk-margin-remove">Место: <span class="uk-text-success" style="font-weight: 700;"><?php echo $ticket_page->seat; ?></span></p>
        <p class="uk-margin-remove">ID билета: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $ticket_page->id; ?></span></p>

        <form class="uk-flex uk-flex-column" id="select_edit_seat" action="/pravka-bileta-forma/" method="post">
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="sb_idbus" type="text" name="sb_idbus" value="<?php echo $sb_idbus; ?>">
            </div>
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="id_seat" type="text" name="id_seat" value="<?php echo $sb_del_ticket ?>">
            </div>
            
            <div class="uk-margin-small-top uk-flex uk-flex-column">
                <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Назад</button>
            </div>
        </form>

        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>
	
<?php   
}
?>