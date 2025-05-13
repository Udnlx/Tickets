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
$sb_reg_ticket = !empty($_POST['sb_reg_ticket'])?$_POST['sb_reg_ticket']:NULL;
$ticket_page = $pages->get('template=purchased_tickets, id=' . $sb_reg_ticket . '');
$sb_seat = $ticket_page->seat;
if ($ticket_page->sb_ticket_id) {
    $run_operation = 'off';
    $sb_seat_id = '';
} else {
    $run_operation = 'on';
}

$success = 'Билет успешно проведен в 1С';
$log = '';
if ($sb_idbus && $sb_reg_ticket && $sb_seat && $run_operation == 'on') {
    // echo $sb_idbus;
    // echo $sb_seat;
    
    //Регистрируем билет в 1С
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

    //Получаем свободные места
    try{
        $dataSeat = $client->getRaceSeats(["raceCode"=>'' . $sb_idbus . '']);
    }
    catch (SoapFault $soapFault){
        echo '<h2>Не удалось вызвать функцию</h2>';
        echo '<pre>'; 
        var_dump($soapFault);
        echo '</pre>';
    }

    $array_seat = json_decode($dataSeat->return, JSON_UNESCAPED_UNICODE);
    // echo '<pre>'; 
    // var_dump($array_seat);
    // echo '</pre>';
    //Получаем свободные места

    //Проверяем, свободно ли место которое сейчас будем регистрировать и получаем его ID
    $sb_free_seats = [];
    foreach ($array_seat as $seat) {
        $str = mb_substr($seat['name'], -2, 2);
        $str = sprintf("%02d", $str);
        if ($str == $sb_seat) {
            $sb_seat_id = $seat['code'];
            //echo $sb_seat_id;
        }
        $sb_free_seats[] = $str;
    }
    // foreach ($sb_free_seats as $sb_free_seat) {
    //     $sb_log .=  $sb_free_seat . ', ';
    // }
    if (in_array("00", $sb_free_seats)) {
        $success = 'Регистрация в 1С не прошла, получен нулевой массив мест';
    }
    if (!in_array($sb_seat, $sb_free_seats)) {
        $success = 'Регистрация в 1С не прошла, место уже занято';
        $seat_busy = 'on';
    } else {
        $seat_busy = 'off';
    }
    //Проверяем, свободно ли место которое сейчас будем регистрировать и получаем его ID

    //Проводим билет
    if ($seat_busy == 'off') {
        $passenger_page = $pages->get('template=passengers, id=' . $ticket_page->id_passenger . '');

        $sb_birthday = $passenger_page->birthday_passenger;
        $old_date_timestamp = strtotime($sb_birthday);
        $sb_birthday = date('Y-m-d', $old_date_timestamp);
        $sb_docnum = $passenger_page->num_doc_passenger;
        $sb_docseries = $passenger_page->passport_passenger;
        $sb_passengername = $passenger_page->name_passenger;
        $parts_name = explode(' ', $sb_passengername);
        $sb_gender = $passenger_page->gender_passenger;
        if ($sb_gender == 'М') {
            $sb_gender = 'M';
        }
        if ($sb_gender == 'Ж') {
            $sb_gender = 'F';
        }
        $sb_phone = $passenger_page->phone_passenger;

        $fr_racecode = $sb_idbus;
        $fr_birthday = $sb_birthday;
        $fr_docnum = $sb_docnum;
        $fr_docseries = $sb_docseries;
        $fr_firstname = $parts_name[1];
        $fr_gender = $sb_gender;
        $fr_lastname = $parts_name[0];
        $fr_middlename = $parts_name[2];
        $fr_phone = $sb_phone;
        $fr_seatcode = $sb_seat_id;

        // echo $fr_racecode . $fr_birthday . $fr_docnum . $fr_docseries . $fr_firstname . $fr_gender . $fr_lastname . $fr_middlename . $fr_phone . $fr_seatcode;

        try{
            $dataList = $client->bookOrder([
                "raceCode" => $fr_racecode,
                'sales' => json_encode([
                    [
                        'birthday' => $fr_birthday,
                        'citizenship' => 'RU',
                        'docNum' => $fr_docnum,
                        'docSeries' => $fr_docseries,
                        'docTypeCode' => '1',
                        'firstName' => $fr_firstname,
                        'gender' => $fr_gender,
                        'lastName' => $fr_lastname,
                        'middleName' => $fr_middlename,
                        'phone' => $fr_phone,
                        'seatCode' => $fr_seatcode,
                        'ticketTypeCode' => '1#1#1',
                    ]
                ]),
            ]);
        }
        catch (SoapFault $soapFault){
            echo '<h2>Не удалось вызвать функцию</h2>';
            echo '<pre>'; 
            var_dump($soapFault);
            echo '</pre>';
        }

        $answer_book_order = json_decode($dataList->return, JSON_UNESCAPED_UNICODE);
        // echo '<pre>'; 
        // var_dump($answer_book_order);
        // echo '</pre>';
        // echo $answer_book_order['id'];

        try{
            $dataList = $client->confirmOrder(["orderId"=>$answer_book_order['id'],"paymentMethod"=>'Безналичный расчет']);
        }
        catch (SoapFault $soapFault){
            echo '<h2>Не удалось вызвать функцию</h2>';
            echo '<pre>'; 
            var_dump($soapFault);
            echo '</pre>';
        }
            
        $answer_confirm_order = json_decode($dataList->return, JSON_UNESCAPED_UNICODE);
        // echo '<pre>'; 
        // var_dump($answer_confirm_order);
        // echo '</pre>';
        // echo $answer_confirm_order['tickets'][0]['id'];

        //Записываем регистрация билета в 1С в лог
        $id_edit_ticket = $sb_reg_ticket;
        $log = '';
        $log .= date('Y-m-d H:i:s') . ' - Билету с ID ' . $id_edit_ticket . ' присвоен ID билета в 1С: ' . $answer_confirm_order['tickets'][0]['id'] . '';
        file_put_contents(__DIR__ . '/log_agent_1c_ticket_registration.txt', $log . PHP_EOL, FILE_APPEND);
        //Записываем регистрация билета в 1С в лог

        //Записываем 1С ID билета в билет
        $edit_page = $pages->get('template=purchased_tickets, id=' . $id_edit_ticket . '');
        $edit_page->of(false);
        $edit_page->sb_ticket_id = $answer_confirm_order['tickets'][0]['id'];
        $edit_page->save();
        //Записываем 1С ID билета в билет
    }
    //Проводим билет

    //Регистрируем билет в 1С
} else {
    $success = 'Билет не проведен в 1С!<br>Ошибка в данных';
    if ($run_operation == 'off') {
        $success = 'Билет уже проведен в 1С!<br>Провести повторно не возможно';
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
	<h1 class="uk-heading-hero uk-text-center">Билет не проведен в 1С</h1>
	            
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
        <p class="uk-margin-remove">ID автобуса в 1С системе: <span style="font-weight: 700;"><?php echo $sb_idbus; ?></span></p>
        <p class="uk-margin-remove">Дата и время отправления: <span style="font-weight: 700;"><?php echo $ticket_page->date_depart; ?> <?php echo $ticket_page->time_depart; ?></span></p>
        <p class="uk-margin-remove">Место: <span class="uk-text-success" style="font-weight: 700;"><?php echo $ticket_page->seat; ?></span></p>
        <p class="uk-margin-remove">ID места в 1С системе: <span class="uk-text-success" style="font-weight: 700;"><?php echo $sb_seat_id; ?></span></p>
        <p class="uk-margin-remove">ID билета: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $ticket_page->id; ?></span></p>
        <p class="uk-margin-remove">ID билета  в 1С системе: <span class="uk-text-danger" style="font-weight: 700;"><?php echo $ticket_page->sb_ticket_id ?></span></p>

        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>
	
<?php   
}
?>