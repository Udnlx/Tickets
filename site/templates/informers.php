<?php namespace ProcessWire;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

$access = '';
if(isset($_SESSION['access'])){
    $access = $_SESSION['access'];
}

$informer_button = '
    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-reisu-vybor-avtobusa/">Отчет по рейсу</a>
';
if ($operator == 'admin-test' || $operator == 'Директор' || $operator == 'Пользователь') {
    $informer_button .= '
    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-reisu-agenty-vybor-avtobusa/">Отчет по рейсу - по агентам</a>
    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-reisu-i-agentam-za-period-vybor-avtobusa/">Отчет по рейсу и агенту за период<br><span style="font-size:10px">(по дате регистрации билета)</span></a>
    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-reisu-i-agentam-za-period-dd-vybor-avtobusa/">Отчет по рейсу и агенту за период<br><span style="font-size:10px">(по дате выезда)</span></a>
    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-agentu-data-registratcii-vybor-agenta/">Отчет по агенту<br><span style="font-size:10px">(по дате регистрации билета)</span></a>
    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-agentu-vybor-agenta/">Отчет по агенту<br><span style="font-size:10px">(по дате выезда)</span></a>
    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-registratcii-passazhirov-vybor-perioda/">Отчет по регистрации пассажиров</a>
    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-biletam-vybor-parametrov/">Отчет по билетам</a>
    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-prodazhe-komissiia-za-period-vybor-avtobusa/">Отчет по продаже комиссия за период</a>
    ';
}
if ($operator == 'Сидорова') {
    $informer_button .= '
    <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-reisu-agenty-vybor-avtobusa/">Отчет по рейсу - по агентам</a>
    ';
}
$informer_button .= '
    <a class="uk-margin-small uk-button uk-button-default" href="/">Назад</a>
';



if ($operator == 'no_operator') {
?>
    <div id="content" style="max-width: 700px;">
    	<h1 class="uk-heading-hero uk-text-center">Отчеты</h1>          
        <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
            <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
            <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
        </div>
    </div>
<?php    
} else {
    if ($access == 'admin' || $access == 'supermanager' || $access == 'manager' || $access == 'managerEditor' || $access == 'managerReserver' || $access == 'managerReports' || $access == 'operator') {
    ?>
        <div id="content" style="max-width: 700px;">
            <h1 class="uk-heading-hero uk-text-center">Отчеты</h1>
                        
            <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
                <h3 class="uk-card-title">Выберите действие</h3>
                <?php echo $informer_button; ?>
            </div>
        </div>
    <?php
    } else {
    ?>
        <div id="content" style="max-width: 700px;">
            <h1 class="uk-heading-hero uk-text-center">Отчеты<h1>
            <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
                <h3 class="uk-card-title">К этой странице у Вас нет доступа</h3>
                <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя</a>
            </div>
        </div>
    <?php
    }
}
?>