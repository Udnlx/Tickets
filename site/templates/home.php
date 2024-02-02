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
    	<h1 class="uk-heading-hero uk-text-center">Домашняя страница</h1>
        <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
            <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
            <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
        </div>
    </div>
<?php    
} else {
?>

<?php
//===============Блок отображения контента в зависимости от прав пользователя===============
$button_informers = '';
if ($access == 'admin') {
    $button_informers .= '<a class="uk-margin-small uk-button uk-button-default" href="/reestr-passazhirov-vybor-passazhira/">Редактор пассажиров</a>';
}
if ($access == 'manager' || $access == 'admin') {
    $button_informers .= '<a class="uk-margin-small uk-button uk-button-default" href="/otchety/">Отчеты</a>';
}
//===============Блок отображения контента в зависимости от прав пользователя===============
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Домашняя страница</h1>
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Выберите действие</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/registratciia-bileta-vybor-reisa/">Зарегистрировать билет</a>
        <a class="uk-margin-small uk-button uk-button-default" href="/pravka-bileta-vybor-reisa/">Правка билета</a>
        <?php echo $button_informers; ?>
        <a class="uk-margin-small uk-button uk-button-default" href="" uk-toggle="target: #modal-help">Техподдержка</a>
    </div>
    
    <!-- Модальное окно техподдержки-->
    <div id="modal-help" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Техподдержка</h2>
            <div id="help_messages" class="messages-block">
                <p class="messages" style="color: green;"></p>
            </div>
                  
            <form class="uk-flex uk-flex-column" id="help_form" action="/tekhpodderzhka/" method="post" enctype="multipart/form-data">        
                <div class="uk-margin-small-top">
                    <input class="uk-input" id="name_operator" type="text" name="name_operator" value="" placeholder="Имя оператора" autocomplete="off" required>
                </div>
                <div class="uk-margin-small-top">
                    <input class="uk-input" id="contact_operator" type="text" name="contact_operator" value="" placeholder="Укажите почту или телефон для связи" required>
                </div>
                <div class="uk-margin-small-top">
                    <textarea class="uk-textarea" rows="7" id="message" name="message" value="" placeholder="Опишите проблему" required></textarea>
                </div>
                <div class="uk-margin-small-top">
                    <p class="uk-input-label" style="margin:0;">Прикрепите скриншоты при необходимости:</p>
                    <input class="uk-input" id="files" type="file" name="file[]" value="" multiple>
                </div>
                <br>
                <div class="uk-flex uk-flex-center">
                    <button id="help_submit" class="uk-button uk-button-primary" type="submit">Отправить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php   
}
?>