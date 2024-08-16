<?php namespace ProcessWire;
    
$name_client = !empty($_POST['name_client'])?$_POST['name_client']:NULL;  
$contact_client = !empty($_POST['contact_client'])?$_POST['contact_client']:NULL;  
$message_client = !empty($_POST['message_client'])?$_POST['message_client']:NULL; 

$msg = 'Имя: ' . $name_client . '<br>';
$msg .= 'Контакты для связи: ' . $contact_client . '<br>';
$msg .= 'Сообщение: ' . $message_client . '<br>';

$to = 'info@niki-group.ru';

$EOL = "\r\n"; // ограничитель строк, некоторые почтовые сервера требуют \n - подобрать опытным путём
$boundary     = "--".md5(uniqid(time()));  // любая строка, которой не будет ниже в потоке данных. 

$subject= 'Обратная связь с сайта "Tickets"';

$headers    = "MIME-Version: 1.0;$EOL";   
$headers   .= "Content-Type: multipart/mixed; boundary=\"$boundary\"$EOL";  

$multipart  = "--$boundary$EOL";   
$multipart .= "Content-Type: text/html; charset=utf-8$EOL";   
$multipart .= "Content-Transfer-Encoding: base64$EOL";   
$multipart .= $EOL; // раздел между заголовками и телом html-части 
$multipart .= chunk_split(base64_encode($msg));   

$multipart .= "$EOL--$boundary--$EOL";

if ($name_client && $contact_client && $message_client) {
    $send_message = '
    <h3 class="uk-card-title">Сообщение отпраленно!</h3>
    <p class="uk-text-warning uk-text-bold uk-text-center">Спасибо за ваше обращение,<br>постараемся исправить проблему как можно быстрее</p>
    ';
    mail($to, $subject, $multipart, $headers);
} else {
    $send_message = '
    <h3 class="uk-card-title" style="color: red;">Сообщение не отпраленно!</h3>
    <p class="uk-text-warning uk-text-bold uk-text-center">Возможно какие-то неполадки на почтовом сервере, попробуйте позже пожалуйста</p>
    ';
}

// if(!mail($to, $subject, $multipart, $headers)){
//     //echo 'Письмо не отправлено';
//     $send_message = '
//     <h3 class="uk-card-title" style="color: red;">Сообщение не отпраленно!</h3>
//     <p class="uk-text-warning uk-text-bold uk-text-center">Возможно какие-то неполадки на почтовом сервере, попробуйте позже пожалуйста</p>
//     ';
// } //Отправляем письмо
// else{
//     //echo 'Письмо отправлено';
//     $send_message = '
//     <h3 class="uk-card-title">Сообщение отпраленно!</h3>
//     <p class="uk-text-warning uk-text-bold uk-text-center">Спасибо за ваше обращение,<br>постараемся исправить проблему как можно быстрее</p>
//     ';
// }
    
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Отправка</h1>
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <?php echo $send_message ?>
        <a class="uk-margin-small uk-button uk-button-default" href="/voznikla-problema-forma/">Назад</a>
    </div>
</div>