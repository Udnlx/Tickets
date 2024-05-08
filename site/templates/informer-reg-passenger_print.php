<?php namespace ProcessWire;

$start_date = !empty($_POST['start_date'])?$_POST['start_date']:NULL;
$finish_date = !empty($_POST['finish_date'])?$_POST['finish_date']:NULL;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Печать отчета по регистрации пассажиров</h1>
	
	            
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
//echo '<pre>'; print_r($arr_all_passengers); echo '</pre>';

$title = array
(
'Отчет регистрации пассажиров ' . $start_date . ' - ' . $finish_date,
'',
);

$headers = array(
    array(
        'name_passenger' => 'ФИО',
        'phone_passenger' => 'Телефон'
    ),   
);

header('Content-Type: text/csv; charset=utf-8' );
header(sprintf( 'Content-Disposition: attachment; filename=Отчет регистрации пассажиров - %s.csv', date( 'dmY-His' ) ) );
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public'); 

$buffer = fopen('php://output', 'w');
foreach ($title as $line) {
    $line = mb_convert_encoding($line, 'windows-1251', 'utf-8');
    fputcsv($buffer,explode(',',$line));
}
foreach($headers as $val) { 
    $val = mb_convert_encoding($val, 'windows-1251', 'utf-8');
	fputcsv($buffer, $val, ';'); 
} 
foreach($arr_all_passengers as $val) { 
    $val = mb_convert_encoding($val, 'windows-1251', 'utf-8');
	fputcsv($buffer, $val, ';'); 
} 
fclose($buffer); 
exit();
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Печать отчета по регистрации пассажиров</h1>
	
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
        <h4 class="uk-margin-remove">Дата: с <span style="font-weight: 700;"><?php echo $start_date; ?></span> по <span style="font-weight: 700;"><?php echo $finish_date; ?></span></h4>
        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>





<?php   
}
?>