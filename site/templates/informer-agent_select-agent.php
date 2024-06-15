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
    	<h1 class="uk-heading-hero uk-text-center">Выбор агента для отчета</h1>      
        <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
            <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
            <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
        </div>
    </div>
<?php    
} else {
    if ($access == 'admin' || $access == 'supermanager' || $access == 'manager') {
    ?>

        <div id="content" style="max-width: 700px;">
            <h1 class="uk-heading-hero uk-text-center">Выбор агента для отчета</h1>
                        
            <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <form class="uk-flex uk-flex-column" id="select_agent" action="/otchet-po-agentu-otchet/" method="post">
                    <?php
                    $all_agents = $pages->get('template=agents');
                    $agents = '';
                    foreach ($all_agents->agent_items as $agent_itm) {
                        $agents .= '
                        <option>' . $agent_itm->agent . '</option>
                        ';
                    }
                    ?>
                    <div class="uk-margin-small-top">
                        <label for="agent">Агент</label>
                        <select class="uk-select" id="agent" name="agent" required>
                            <option></option>
                            <?php echo $agents; ?>
                        </select>
                    </div>

                    <div class="uk-margin-small-top">
                        <label for="start_date">Дата с</label>
                        <input class="uk-input" id="start_date" type="date" name="start_date" required>
                    </div>

                    <div class="uk-margin-small-top">
                        <label for="finish_date">Дата по</label>
                        <input class="uk-input" id="finish_date" type="date" name="finish_date" required>
                    </div>

                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                    <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Далее</button>
                    <a class="uk-margin-small uk-button uk-button-default" href="/otchety/">Назад</a>
                    </div>
                </form>
            </div>
        </div>

    <?php
    } else {
    ?>
        <div id="content" style="max-width: 700px;">
            <h1 class="uk-heading-hero uk-text-center">Выбор агента для отчета</h1>      
            <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
                <h3 class="uk-card-title">К этой странице у Вас нет доступа</h3>
                <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя</a>
            </div>
        </div>
    <?php
    } 
}
?>