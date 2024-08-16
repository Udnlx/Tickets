<?php namespace ProcessWire;

?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Форма обратной связи</h1>
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Заполните форму</h3>
        <form class="uk-flex uk-flex-column" id="help_form" action="/obratnaia-sviaz-otpravka/" method="post">        
            <div class="uk-margin-small-top">
                <input class="uk-input" id="name_client" type="text" name="name_client" value="" placeholder="Имя" autocomplete="off" required>
            </div>
            <div class="uk-margin-small-top">
                <input class="uk-input" id="contact_client" type="text" name="contact_client" value="" placeholder="Укажите почту или телефон для связи" required>
            </div>
            <div class="uk-margin-small-top">
                <textarea class="uk-textarea" rows="7" id="message_client" name="message_client" value="" placeholder="Опишите проблему" required></textarea>
            </div>
            <br>
            <div class="uk-flex uk-flex-center">
                <button id="help_submit" class="uk-button uk-button-primary" type="submit">Отправить</button>
            </div>
        </form>
    </div>

</div>