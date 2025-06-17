<?php

$log = [];
if ($input->get['id']) {

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
        $log[] = 'Подключение прошло успешно';
    }
    catch (SoapFault $soapFault){
        // echo 'Не подключились';
        // echo '<pre>'; 
        // var_dump($soapFault);
        // echo '</pre>';
        $log[] = 'Не подключились';
        $result = setError(json_encode($soapFault), $result, 404);
    }

    try{
        $dataList = $client->returnTicket(["ticketId"=>$input->get['id']]);
        $log[] = 'Функция на сервер отправлена';
    }
    catch (SoapFault $soapFault){
        // echo 'Не удалось вызвать функцию';
        // echo '<pre>'; 
        // var_dump($soapFault);
        // echo '</pre>';
        $log[] = 'Не удалось вызвать функцию';
        $result = setError(json_encode($soapFault), $result, 404);
    }

    // echo ($dataList->return);

    $result["log"] = $log;
    $result["dataList"] = json_decode($dataList->return, JSON_UNESCAPED_UNICODE);

} else {
    $result = setError('Не верно указан параметр запроса', $result, 404);
}