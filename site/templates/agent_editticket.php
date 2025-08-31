<?php //namespace ProcessWire;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

$id_edit_ticket = !empty($_POST['id_edit_ticket'])?$_POST['id_edit_ticket']:NULL;

$old_station_start = !empty($_POST['old_station_start'])?$_POST['old_station_start']:NULL;  
$selected_station_start = !empty($_POST['selected_station_start'])?$_POST['selected_station_start']:NULL;  
$selected_station_start_array = preg_split('/[—]/u', $selected_station_start, -1, PREG_SPLIT_NO_EMPTY);
$selected_station_start_name = $selected_station_start_array[0];
$id_selected_station_start = !empty($_POST['id_selected_station_start'])?$_POST['id_selected_station_start']:NULL;  

$old_station_finish = !empty($_POST['old_station_finish'])?$_POST['old_station_finish']:NULL;  
$selected_station_finish = !empty($_POST['selected_station_finish'])?$_POST['selected_station_finish']:NULL;  
$selected_station_finish_array = preg_split('/[—]/u', $selected_station_finish, -1, PREG_SPLIT_NO_EMPTY);
$selected_station_finish_name = $selected_station_finish_array[0];
$id_selected_station_finish = !empty($_POST['id_selected_station_finish'])?$_POST['id_selected_station_finish']:NULL;  

$old_pay_or_booking = !empty($_POST['old_pay_or_booking'])?$_POST['old_pay_or_booking']:NULL;  
$pay_or_booking = !empty($_POST['pay_or_booking'])?$_POST['pay_or_booking']:NULL;  
$old_booking_sum = !empty($_POST['old_booking_sum'])?$_POST['old_booking_sum']:NULL;  
$booking_sum = !empty($_POST['booking_sum'])?$_POST['booking_sum']:NULL;  
$confirm = !empty($_POST['confirm'])?$_POST['confirm']:NULL;  
$old_type_ticket = !empty($_POST['old_type_ticket'])?$_POST['old_type_ticket']:NULL;  
$type_ticket = !empty($_POST['type_ticket'])?$_POST['type_ticket']:NULL;  
$old_agent_ticket = !empty($_POST['old_agent_ticket'])?$_POST['old_agent_ticket']:NULL;  
$agent_ticket = !empty($_POST['agent_ticket'])?$_POST['agent_ticket']:NULL;  

$old_price_ticket = !empty($_POST['old_price_ticket'])?$_POST['old_price_ticket']:NULL;  
$price_ticket = !empty($_POST['price_ticket'])?$_POST['price_ticket']:NULL;  

$ticket = $pages->get('template=purchased_tickets, id=' . $id_edit_ticket . '');

//Получаем 1С ID автобуса
$old_sb_bus_id = $ticket->sb_bus_id;
$new_sb_bus_id = '';

$id_bus = $ticket->id_bus;
$page_bus = $pages->get('template=buses_item, id=' . $id_bus . '');
$sbid_station_start = '';
$sbid_station_finish = '';
$sb_dispatch_date = $ticket->date_depart;
foreach ($page_bus->table_price as $item) {
    if (trim($item->name_station) == trim($selected_station_start_name) && trim($item->name_station_finish) == trim($selected_station_finish_name)) {
        $sbid_station_start = $item->sbid_station_start;
        $sbid_station_finish = $item->sbid_station_finish;
    }
}
// echo $sbid_station_start;
// echo $sbid_station_finish;
// echo $sb_dispatch_date;

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
    // echo '<h2>Подключение прошло успешно</h2>';
}
catch (SoapFault $soapFault){
    // echo '<h2>не подключились</h2>';
    // echo '<pre>'; 
    // var_dump($soapFault);
    // echo '</pre>';
}

try{
    $dataList = $client->getRaces(["dispatchPlaceId"=>$sbid_station_start,"arrivalPlaceId"=>$sbid_station_finish,"date"=>$sb_dispatch_date]);
    // echo '<h2>Функция на сервер отправлена</h2>';
}
catch (SoapFault $soapFault){
    // echo '<h2>не удалось вызвать функцию</h2>';
    // echo '<pre>'; 
    // var_dump($soapFault);
    // echo '</pre>';
}

$dataListjson = json_decode($dataList->return, JSON_UNESCAPED_UNICODE);
// echo '<pre>'; 
// var_dump($dataListjson);
// echo '</pre>';

$new_sb_bus_id = $old_sb_bus_id;
$array = $dataListjson[0];
if ($array['uid']) {
    $new_sb_bus_id = $array['uid'];
    //echo $uid_bus;
} else {
    //echo 'Автобус не найден';
}
//Получаем 1С ID автобуса

$success = 'Правки билета успешно внесены';
$log = '';
if ($id_edit_ticket && $selected_station_start && $selected_station_finish && $pay_or_booking && $confirm && $type_ticket && $agent_ticket && $price_ticket) {
    $log .= date('Y-m-d H:i:s') . ' - Изменен билет id - ' . $id_edit_ticket . '. ';
    $log .= 'Параметры измененного билета: ' . $ticket->bus . ' ' . $ticket->date_depart . '' . $ticket->time_depart . ', id автобуса - ' . $ticket->id_bus . ', место - ' . $ticket->seat . ', пассажир - ' . $ticket->passenger . '; '; 
    $log .= 'Оператор изменений: ' . $operator . '; '; 
    $log .= 'Станция посадки изменена с ' . $old_station_start . ' на ' . $selected_station_start_name . '; '; 
    $log .= 'Станция высадки изменена с ' . $old_station_finish . ' на ' . $selected_station_finish_name . '; '; 
    $log .= 'Оплачен или бронь изменен с ' . $old_pay_or_booking . ' на ' . $pay_or_booking . '; '; 
    $log .= 'Сумма к оплате изменена с ' . $old_booking_sum . ' на ' . $booking_sum . '; '; 
    $log .= 'Статус подтверждения изменен на ' . $confirm . '; '; 
    $log .= 'Тип билета изменен с ' . $old_type_ticket . ' на ' . $type_ticket . '; '; 
    $log .= 'Агент изменен с ' . $old_agent_ticket . ' на ' . $agent_ticket . '; '; 
    $log .= 'Цена билета изменена с ' . $old_price_ticket . ' на ' . $price_ticket . '; '; 
    $log .= '1C ID автобуса изменено с ' . $old_sb_bus_id . ' на ' . $new_sb_bus_id . '; '; 
    file_put_contents(__DIR__ . '/log_edit_tikets.txt', $log . PHP_EOL, FILE_APPEND);
    
    $edit_page = $pages->get('template=purchased_tickets, id=' . $id_edit_ticket . '');
    $edit_page->of(false);
    $edit_page->id_station = $id_selected_station_start;
    $edit_page->name_station = $selected_station_start;
    $edit_page->id_station_finish = $id_selected_station_finish;
    $edit_page->name_station_finish = $selected_station_finish;
    $edit_page->pay_or_booking = $pay_or_booking;
    $edit_page->booking_sum = $booking_sum;
    $edit_page->confirm = $confirm;
    $edit_page->type_ticket = $type_ticket;
    $edit_page->agent_ticket = $agent_ticket;
    $edit_page->price_ticket = $price_ticket;
    $edit_page->sb_bus_id = $new_sb_bus_id;
    $edit_page->save();
} else {
    $success = 'Правки билета не внесены!<br>Ошибка в данных';
}



if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
    <h1 class="uk-heading-hero uk-text-center">Правки билета не внесены</h1>
    
                
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
        <p class="uk-margin-remove">Автобус: <span style="font-weight: 700;"><?php echo $ticket->bus; ?></span></p>
        <p class="uk-margin-remove">ID автобуса: <span style="font-weight: 700;"><?php echo $ticket->id_bus; ?></span></p>
        <p class="uk-margin-remove">Дата и время отправления: <span style="font-weight: 700;"><?php echo $ticket->date_depart; ?> <?php echo $ticket->time_depart; ?></span></p>
        <p class="uk-margin-remove">Место: <span style="font-weight: 700;"><?php echo $ticket->seat; ?></span></p>
        <p class="uk-margin-remove">Пассажир: <span style="font-weight: 700;"><?php echo $ticket->passenger; ?></span></p>
        <p class="uk-margin-remove">ID билета: <span style="font-weight: 700;"><?php echo $id_edit_ticket; ?></span></p>
        <br>
        <p class="uk-margin-remove">Станция посадки старое значение: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $old_station_start; ?></span></p>
        <p class="uk-margin-remove">Изменено на: <span class="uk-text-success" style="font-weight: 700;"><?php echo $selected_station_start_name; ?></span></p>
        <br>

        <p class="uk-margin-remove">Станция высадки старое значение: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $old_station_finish; ?></span></p>
        <p class="uk-margin-remove">Изменено на: <span class="uk-text-success" style="font-weight: 700;"><?php echo $selected_station_finish_name; ?></span></p>
        <br>

        <p class="uk-margin-remove">Оплачен или бронь старое значение: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $old_pay_or_booking; ?></span></p>
        <p class="uk-margin-remove">Изменено на: <span class="uk-text-success" style="font-weight: 700;"><?php echo $pay_or_booking; ?></span></p>
        <br>

        <p class="uk-margin-remove">Сумма к оплате старое значение: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $old_booking_sum; ?></span></p>
        <p class="uk-margin-remove">Изменено на: <span class="uk-text-success" style="font-weight: 700;"><?php echo $booking_sum; ?></span></p>
        <br>

        <p class="uk-margin-remove">Статус подтверждения изменен на: <span class="uk-text-success" style="font-weight: 700;"><?php echo $confirm; ?></span></p>
        <br>

        <p class="uk-margin-remove">Тип билета старое значение: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $old_type_ticket; ?></span></p>
        <p class="uk-margin-remove">Изменено на: <span class="uk-text-success" style="font-weight: 700;"><?php echo $type_ticket; ?></span></p>
        <br>

        <p class="uk-margin-remove">Агент старое значение: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $old_agent_ticket; ?></span></p>
        <p class="uk-margin-remove">Изменено на: <span class="uk-text-success" style="font-weight: 700;"><?php echo $agent_ticket; ?></span></p>
        <br>

        <p class="uk-margin-remove">Цена билета старое значение: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $old_price_ticket; ?></span></p>
        <p class="uk-margin-remove">Изменено на: <span class="uk-text-success" style="font-weight: 700;"><?php echo $price_ticket; ?></span></p>
        <br>

        <p class="uk-margin-remove">1C ID автобуса старое значение: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $old_sb_bus_id; ?></span></p>
        <p class="uk-margin-remove">Изменено на: <span class="uk-text-success" style="font-weight: 700;"><?php echo $new_sb_bus_id; ?></span></p>
        <br>

        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>
    
<?php   
}
?>