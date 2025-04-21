<?php

error_reporting(E_ERROR | E_PARSE);

$sb_log = '';
$seat_busy = 'off';

$sb_idbus = $_POST['sb_idbus'];
$sb_seat = $_POST['sb_seat'];

// $sb_log .= $sb_idbus;
// $sb_log .= $sb_seat;

if ($sb_idbus && $sb_seat) {

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
    $sb_log .= '<p style="color:green;margin:0;">Подключение к 1C прошло успешно</p>';
}
catch (SoapFault $soapFault){
    $sb_log .=  '<p style="color:red;margin:0;">Не подключились к 1C</p>';
    $info_json = json_encode($soapFault);
    $sb_log .=  '<p style="color:red;">' . $info_json . '</p>';
}
//Подключаемся



//Получаем свободные места
try{
    $dataSeat = $client->getRaceSeats(["raceCode"=>'' . $sb_idbus . '']);
}
catch (SoapFault $soapFault){
    $sb_log .=  '<p style="color:red;margin:0;">Не удалось вызвать функцию</p>';
    $info_json = json_encode($soapFault);
    $sb_log .=  '<p style="color:red;">' . $info_json . '</p>';
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
    $sb_log .=  '<p style="color:red;margin:0;">Регистрация в 1С не прошла, получен нулевой массив мест</p>';
}
if (!in_array($sb_seat, $sb_free_seats)) {
    $sb_log .=  '<p style="color:red;margin:0;">Регистрация в 1С не прошла, место уже занято</p>';
    $seat_busy = 'on';
} else {
    $seat_busy = 'off';
}
//Проверяем, свободно ли место которое сейчас будем регистрировать и получаем его ID



//Регистрируем билет
if ($seat_busy == 'off') {
    $sb_birthday = $_POST['sb_birthday'];
    $old_date_timestamp = strtotime($sb_birthday);
    $sb_birthday = date('Y-m-d', $old_date_timestamp);
    $sb_docnum = $_POST['sb_docnum'];
    $sb_docseries = $_POST['sb_docseries'];
    $sb_passengername = $_POST['sb_passengername'];
    $parts_name = explode(' ', $sb_passengername);
    $sb_gender = $_POST['sb_gender'];
    if ($sb_gender == 'М') {
        $sb_gender = 'M';
    }
    if ($sb_gender == 'Ж') {
        $sb_gender = 'F';
    }
    $sb_phone = $_POST['sb_phone'];

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
        $sb_log .=  '<p style="color:red;margin:0;">Не удалось вызвать функцию</p>';
        $info_json = json_encode($soapFault);
        $sb_log .=  '<p style="color:red;">' . $info_json . '</p>';
    }

    $answer_book_order = json_decode($dataList->return, JSON_UNESCAPED_UNICODE);
    // echo '<pre>'; 
    // var_dump($answer_book_order);
    // echo '</pre>';
    // echo $answer_book_order['id'];

    try{
        $dataList = $client->confirmOrder(["orderId"=>$answer_book_order['id'],"paymentMethod"=>'Безналичный расчет']);
        $sb_log .= '<p style="color:green;margin:0;">Билет успешно зарегистрирован в системе 1С</p>';
    }
    catch (SoapFault $soapFault){
        $sb_log .=  '<p style="color:red;margin:0;">Не удалось вызвать функцию</p>';
        $info_json = json_encode($soapFault);
        $sb_log .=  '<p style="color:red;">' . $info_json . '</p>';
    }
        
    $answer_confirm_order = json_decode($dataList->return, JSON_UNESCAPED_UNICODE);
    // echo '<pre>'; 
    // var_dump($answer_confirm_order);
    // echo '</pre>';
    // echo $answer_confirm_order['tickets'][0]['id'];

    //Записываем регистрация билета в 1С в лог
    $id_edit_ticket = $_POST['sb_idticket'];
    $log = '';
    $log .= date('Y-m-d H:i:s') . ' - Билету с ID ' . $id_edit_ticket . ' присвоен ID билета в 1С: ' . $answer_confirm_order['tickets'][0]['id'] . '';
    file_put_contents(__DIR__ . '/site/templates/log_1c_ticket_registration.txt', $log . PHP_EOL, FILE_APPEND);
    //Записываем регистрация билета в 1С в лог

    $sb_log .= '<p id="id_edit_ticket" class="uk-hidden">' . $id_edit_ticket . '</p>';
    $sb_log .= '<p id="sb_id_ticket" class="uk-hidden">' . $answer_confirm_order['tickets'][0]['id'] . '</p>';
}
//Регистрируем билет



//Выводим результат
echo '<div style="text-align:center;">'. $sb_log . '</div>';
//Выводим результат

} else {

echo '<div style="text-align:center;"><p style="color:red;">Регистрация в 1С не прошла, ошибка в данных</p></div>';

}

