<?php namespace ProcessWire;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Сообщение в техподдержку</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сесия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
    
$name_operator = !empty($_POST['name_operator'])?$_POST['name_operator']:NULL;  
$contact_operator = !empty($_POST['contact_operator'])?$_POST['contact_operator']:NULL;  
$message = !empty($_POST['message'])?$_POST['message']:NULL; 
//$files = $_FILES;
// echo '<pre>';
// print_r($files);
// echo '</pre>';

$msg = 'Оператор: ' . $name_operator . '<br>';
$msg .= 'Контакты для связи: ' . $contact_operator . '<br>';
$msg .= 'Сообщение: ' . $message . '<br>';

$to = 'info@niki-group.ru';

$EOL = "\r\n"; // ограничитель строк, некоторые почтовые сервера требуют \n - подобрать опытным путём
$boundary     = "--".md5(uniqid(time()));  // любая строка, которой не будет ниже в потоке данных. 

$subject= 'Новая заявка в техподдержку с сайта "Tickets"';

$headers    = "MIME-Version: 1.0;$EOL";   
$headers   .= "Content-Type: multipart/mixed; boundary=\"$boundary\"$EOL";  

$multipart  = "--$boundary$EOL";   
$multipart .= "Content-Type: text/html; charset=utf-8$EOL";   
$multipart .= "Content-Transfer-Encoding: base64$EOL";   
$multipart .= $EOL; // раздел между заголовками и телом html-части 
$multipart .= chunk_split(base64_encode($msg));   

#начало вставки файлов

foreach($_FILES["file"]["name"] as $key => $value){
    $filename = $_FILES["file"]["tmp_name"][$key];
    if ($filename) {
    $file = fopen($filename, "rb");
    $data = fread($file,  filesize( $filename ) );
    fclose($file); 
    $NameFile = $_FILES["file"]["name"][$key]; // в этой переменной надо сформировать имя файла (без всякого пути);
    $File = $data;
    $multipart .=  "$EOL--$boundary$EOL";   
    $multipart .= "Content-Type: application/octet-stream; name=\"$NameFile\"$EOL";   
    $multipart .= "Content-Transfer-Encoding: base64$EOL";   
    $multipart .= "Content-Disposition: attachment; filename=\"$NameFile\"$EOL";   
    $multipart .= $EOL; // раздел между заголовками и телом прикрепленного файла 
    $multipart .= chunk_split(base64_encode($File));   
    }
}

#>>конец вставки файлов

$multipart .= "$EOL--$boundary--$EOL";
//mail($to, $subject, $multipart, $headers);

$send_message = '
<h3 class="uk-card-title" style="color: red;">Сообщение не отпраленно!</h3>
<p class="uk-text-warning uk-text-bold uk-text-center">Возможно какие-то неполадки на почтовом сервере, попробуйте позже пожалуйста</p>
';
if(!mail($to, $subject, $multipart, $headers)){
    //echo 'Письмо не отправлено';
    $send_message = '
    <h3 class="uk-card-title" style="color: red;">Сообщение не отпраленно!</h3>
    <p class="uk-text-warning uk-text-bold uk-text-center">Возможно какие-то неполадки на почтовом сервере, попробуйте позже пожалуйста</p>
    ';
} //Отправляем письмо
else{
    //echo 'Письмо отправлено';
    $send_message = '
    <h3 class="uk-card-title">Сообщение отпраленно!</h3>
    <p class="uk-text-warning uk-text-bold uk-text-center">Спасибо за ваше обращение,<br>постараемся исправить проблему как можно быстрее</p>
    ';
}
    
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Сообщение в техподдержку<br>(в разработке)</h1>
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <?php echo $send_message ?>
        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>

<?php   
}
?>