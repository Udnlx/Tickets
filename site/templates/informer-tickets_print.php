<?php namespace ProcessWire;

$user = !empty($_POST['user'])?$_POST['user']:NULL;  
$start_date = !empty($_POST['start_date'])?$_POST['start_date']:NULL;
$finish_date = !empty($_POST['finish_date'])?$_POST['finish_date']:NULL;

$s_date = strtotime($start_date);
$f_date = strtotime($finish_date . ' +1 day');

$filter_user = '';
if ($user == 'Все') {
    $filter_user = '';
} else {
    $filter_user = 'operator=' . $user . ',';
}

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Печать отчета по билетам</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php
$reestr_all_user_tickets = '';

$all_user_tickets = $pages->find('template=purchased_tickets, ' . $filter_user . ' created>=' . $s_date . ', created<=' . $f_date . ', sort=created');
$arr_all_user_tickets = [];
foreach ($all_user_tickets as $all_user_tickets_item) {
    $arr_all_user_tickets[] = array(
        "reg_ticket"=>date("Y-m-d H:i:s", $all_user_tickets_item->published), 
        "operator"=>$all_user_tickets_item->operator,
        "bus"=>$all_user_tickets_item->title,
        "agent"=>$all_user_tickets_item->agent_ticket,
        );
}
//echo '<pre>'; print_r($arr_all_user_tickets); echo '</pre>';

$title = array
(
'Отчет по билетам ' . $user . ' - ' . $start_date . ' - ' . $finish_date,
'',
);

$headers = array(
    array(
        'reg_ticket' => 'Дата регистрации',
        'operator' => 'Оператор',
        'bus' => 'Автобус',
        'agent' => 'Агент',
    ),   
);

$footer = array(
    array(
        'reg_ticket' => '',
        'operator' => '',
        'bus' => '',
        'agent' => '',
    ),   
);

header('Content-Type: text/csv; charset=utf-8' );
header(sprintf( 'Content-Disposition: attachment; filename=Отчет по билетам ' . $user . ' - %s.csv', date( 'dmY-His' ) ) );
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
foreach($arr_all_user_tickets as $val) { 
    $val = mb_convert_encoding($val, 'windows-1251', 'utf-8');
	fputcsv($buffer, $val, ';'); 
} 
foreach($footer as $val) { 
    $val = mb_convert_encoding($val, 'windows-1251', 'utf-8');
    fputcsv($buffer, $val, ';'); 
} 
fclose($buffer); 
exit();
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Печать отчета по билетам</h1>
	
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
        <h4 class="uk-margin-remove">Выбранный оператор: <span style="font-weight: 700;"><?php echo $user; ?></span></h4>
        <h4 class="uk-margin-remove">Дата: с <span style="font-weight: 700;"><?php echo $start_date; ?></span> по <span style="font-weight: 700;"><?php echo $finish_date; ?></span></h4>
        <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
    </div>
</div>





<?php   
}
?>