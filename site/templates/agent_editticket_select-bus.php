<?php namespace ProcessWire;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

$agent_page = $pages->get("template=users_system_item, title=" . $operator . "");

$button_bus = '';

$all_bus = $agent_page->bus_for_agent->find("template=buses_item, bus_group=1, sort=sort");
$button_bus .= '
<ul uk-accordion>
    <li>
        <a class="uk-accordion-title" href="#">Рейсы из Москвы</a>
        <div class="uk-accordion-content">
';
foreach ($all_bus as $bus_item) {
    $title = '';
    $stations = $bus_item->station_start;
    foreach ($stations as $station) {
        $title .= $station->title . '<br>';
    }
    $button_bus .= '
        <button id="' . $bus_item->id . '" class="uk-ticket-button uk-margin-small-top uk-button uk-button-default" uk-tooltip="' . $title . '">' . $bus_item->title . '<br><span>' . $bus_item->option_bus . '</span></button>
    ';
};
$button_bus .= '
        </div>
    </li>
';

$all_bus = $agent_page->bus_for_agent->find("template=buses_item, bus_group=2, sort=sort");
$button_bus .= '
    <li>
        <a class="uk-accordion-title" href="#">Рейсы в Москву</a>
        <div class="uk-accordion-content">
';
foreach ($all_bus as $bus_item) {
    $title = '';
    $stations = $bus_item->station_start;
    foreach ($stations as $station) {
        $title .= $station->title . '<br>';
    }
    $button_bus .= '
        <button id="' . $bus_item->id . '" class="uk-ticket-button uk-margin-small-top uk-button uk-button-default" uk-tooltip="' . $title . '">' . $bus_item->title . '<br><span>' . $bus_item->option_bus . '</span></button>
    ';
}
$button_bus .= '
        </div>
    </li>
</ul>
';

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Выбор рейса для правки билета</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<div id="content">
	<h1 class="uk-heading-hero uk-text-center">Выбор рейса для правки билета</h1>
	<div class="uk-child-width-1-2@m" uk-grid>
	    
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" uk-sticky>
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <h3 class="uk-margin-remove uk-card-title">Выбранный рейс</h3>
                <form class="uk-flex uk-flex-column" id="select_bus" action="/agent-pravka-bileta-vybor-mesta/" method="post">
                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="post_bus" type="text" name="post_bus" placeholder="Выберите рейс из списка" autocomplete="off" required>
                    </div>
                    <p class="uk-margin-remove" style="color: red; font-weight: 700;">ВНИМАНИЕ! Выбирайте дату рейса по его описанию:</p>
                    <p id="option_bus" class="uk-margin-remove" style="font-weight: 700;">Описание рейса</p>
                    
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="post_id_bus" type="text" name="post_id_bus">
                    </div>
                    
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="post_time" type="text" name="post_time">
                    </div>
                    
                    <div class="uk-margin-small-top">
                        <input class="uk-input" id="post_date" type="date" name="post_date" placeholder="Рейс" required>
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                    <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Далее</button>
                    <a class="uk-margin-small uk-button uk-button-default" href="/">Назад</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Список рейсов</h3>
                <?php echo $button_bus; ?>
            </div>
        </div>
        
    </div>
</div>

<?php   
}
?>