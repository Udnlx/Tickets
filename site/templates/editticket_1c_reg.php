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
$sb_seat_id = '';
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
        echo '<h2>Не удалось вызвать функцию getRaceSeats</h2>';
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
            $birth_date = $sb_birthday;  
            $current_date = date('Y-m-d');  
            $birth_timestamp = strtotime($birth_date);  
            $current_timestamp = strtotime($current_date);  
            $diff_seconds = $current_timestamp - $birth_timestamp;  
            $age_years = $diff_seconds / (60 * 60 * 24 * 365.25);  
            $age_years = round($age_years);  
            //echo $age_years;
        $ticket_type_code = '1#1#1';
        if ($age_years <= 11) {
            //echo 'Детский';
            $ticket_type_code = '38#6#1';
        } else {
            //echo 'Взрослый';
            $ticket_type_code = '1#1#1';
        }
        $sb_doc = $passenger_page->type_doc_passenger;
        if ($sb_doc == 'Паспорт РФ') {
            $sb_doc = '1';
        }
        if ($sb_doc == 'Свидетельство о рождении') {
            $sb_doc = '2';
        }
        if ($sb_doc == 'Военный билет') {
            $sb_doc = '3';
        }
        if ($sb_doc == 'Паспорт иностранного пассажира') {
            $sb_doc = '52';
        }
        if ($sb_doc == 'Заграничный паспорт РФ') {
            $sb_doc = '63';
        }
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
        $sb_citizenship = $passenger_page->citizenship_passenger;
        if ($sb_citizenship == '') {
            $sb_citizenship = 'RU';
        }
        $sb_phone = $passenger_page->phone_passenger;
        $sb_phone = preg_replace('/[^0-9]/', '', $sb_phone);
        $sb_phone = substr($sb_phone, 0, 11);

        $fr_racecode = $sb_idbus;
        $fr_birthday = $sb_birthday;
        $fr_doc = $sb_doc;
        $fr_docnum = $sb_docnum;
        $fr_docseries = $sb_docseries;
        $fr_firstname = $parts_name[1];
        $fr_gender = $sb_gender;
        $fr_citizenship = $sb_citizenship;
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
                        'citizenship' => $fr_citizenship,
                        'docNum' => $fr_docnum,
                        'docSeries' => $fr_docseries,
                        'docTypeCode' => $fr_doc,
                        'firstName' => $fr_firstname,
                        'gender' => $fr_gender,
                        'lastName' => $fr_lastname,
                        'middleName' => $fr_middlename,
                        'phone' => $fr_phone,
                        'seatCode' => $fr_seatcode,
                        'ticketTypeCode' => $ticket_type_code,
                    ]
                ]),
            ]);
        }
        catch (SoapFault $soapFault){
            echo '<h2>Не удалось вызвать функцию bookOrder</h2>';
            echo '<pre>'; 
            var_dump($soapFault);
            echo '</pre>';
        }

        $answer_book_order = json_decode($dataList->return, JSON_UNESCAPED_UNICODE);
        // echo '<pre>'; 
        // var_dump($answer_book_order);
        // echo '</pre>';
        // echo $answer_book_order['id'];

        $sb_error = '';
        if (!$answer_book_order['id']) {
            echo '<pre>'; 
            var_dump($answer_book_order);
            echo '</pre>';
            $sb_error = json_encode($answer_book_order, JSON_UNESCAPED_UNICODE);
        }

        try{
            $dataList = $client->confirmOrder(["orderId"=>$answer_book_order['id'],"paymentMethod"=>'Безналичный расчет']);
        }
        catch (SoapFault $soapFault){
            echo '<h2>Не удалось вызвать функцию confirmOrder</h2>';
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
        $log .= ' Билет проводился со страницы редактирования билета, оператор: ' . $operator . ' ';
        $log .= $sb_error;
        file_put_contents(__DIR__ . '/log_1c_ticket_registration.txt', $log . PHP_EOL, FILE_APPEND);
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

if ($operator == 'no_operator' || $access == 'agent') {
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

        <form class="uk-flex uk-flex-column" id="select_edit_seat" action="/pravka-bileta-forma/" method="post">
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="sb_idbus" type="text" name="sb_idbus" value="<?php echo $sb_idbus; ?>">
            </div>
            <div class="uk-margin-small-top uk-hidden">
                <input class="uk-input" id="id_seat" type="text" name="id_seat" value="<?php echo $sb_reg_ticket; ?>">
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