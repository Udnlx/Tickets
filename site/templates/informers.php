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
    if ($access == 'manager' || $access == 'admin') {
    ?>
        <div id="content" style="max-width: 700px;">
            <h1 class="uk-heading-hero uk-text-center">Отчеты</h1>
                        
            <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
                <h3 class="uk-card-title">Выберите действие</h3>
                <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-reisu-vybor-avtobusa/">Отчет по рейсу</a>
                <a class="uk-margin-small uk-button uk-button-default" href="/otchet-po-agentu-vybor-agenta/">Отчет по агенту (в разработке)</a>
                <a class="uk-margin-small uk-button uk-button-default" href="">Отчет по пассажиру (в разработке)</a>
                <a class="uk-margin-small uk-button uk-button-default" href="">Отчет по билетам за период (в разработке)</a>
                <a class="uk-margin-small uk-button uk-button-default" href="/">Назад</a>
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