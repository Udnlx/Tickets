<?php namespace ProcessWire;

$selected_bus = !empty($_POST['selected_bus'])?$_POST['selected_bus']:NULL;  
$selected_id_bus = !empty($_POST['selected_id_bus'])?$_POST['selected_id_bus']:NULL;
$selected_date = !empty($_POST['selected_date'])?$_POST['selected_date']:NULL;
$selected_time = !empty($_POST['selected_time'])?$_POST['selected_time']:NULL;
$selected_seat = !empty($_POST['selected_seat'])?$_POST['selected_seat']:NULL;
$id_seat = !empty($_POST['id_seat'])?$_POST['id_seat']:NULL;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Правка билета</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php
$ticket = $pages->get('template=purchased_tickets, id=' . $id_seat . '');
?>

<div id="content">
	<h1 class="uk-heading-hero uk-text-center">Правка билета</h1>
	<div class="uk-child-width-1-2@m" uk-grid>
	    
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" uk-sticky>
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <h4 class="uk-margin-remove">Выбранный рейс:<br><span style="font-weight: 700;"><?php echo $selected_bus; ?></span></h4>
                <h4 class="uk-margin-remove">Дата: <span style="font-weight: 700;"><?php echo $selected_date; ?></span> отправление<span style="font-weight: 700;"><?php echo $selected_time; ?></span></h4>
                <h4 class="uk-margin-remove">Выбранное место: <span style="font-weight: 700;"><?php echo $selected_seat; ?></span></h4>
                <h4 class="uk-margin-remove">ID билета: <span style="font-weight: 700;"><?php echo $id_seat; ?></span></h4>
                <h4 class="uk-margin-remove">Статус билета: <span style="font-weight: 700;"><?php echo $ticket->pay_or_booking; ?></span></h4>
                <?php
                if ($ticket->booking_sum > 0) {
                ?>
                <h4 class="uk-margin-remove">Сумма к оплате при бронировании: <span style="font-weight: 700;"><?php echo $ticket->booking_sum; ?></span></h4>
                <?php
                }
                ?>
                <h4 class="uk-margin-remove">Статус подтверждения: <span style="font-weight: 700;"><?php echo $ticket->confirm; ?></span></h4>
                <h4 class="uk-margin-remove">Пассажир: <span style="font-weight: 700;"><?php echo $ticket->passenger; ?></span></h4>
                <a class="uk-margin-small uk-button uk-button-default" href="/pravka-bileta-vybor-reisa/">Выбрать другой рейс и место</a>
                <a class="uk-margin-small uk-button uk-button-default" href="/">Вернутся на главную</a>
            </div>
        </div>
        
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Выбрать новые значения</h3>
                <form class="uk-flex uk-flex-column" id="edit_ticket" action="/pravka-bileta-smena-statusa/" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="selected_bus" type="text" name="selected_bus" value="<?php echo $selected_bus; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="selected_id_bus" type="text" name="selected_id_bus" value="<?php echo $selected_id_bus; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="selected_date" type="text" name="selected_date" value="<?php echo $selected_date; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="selected_time" type="text" name="selected_time" value="<?php echo $selected_time; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="selected_seat" type="text" name="selected_seat" value="<?php echo $selected_seat; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="id_seat" type="text" name="id_seat" value="<?php echo $id_seat; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="old_pay_or_booking" type="text" name="old_pay_or_booking" value="<?php echo $ticket->pay_or_booking; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="old_booking_sum" type="text" name="old_booking_sum" value="<?php echo $ticket->booking_sum; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="old_confirm" type="text" name="old_confirm" value="<?php echo $ticket->confirm; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="passenger" type="text" name="passenger" value="<?php echo $ticket->passenger; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top">
                        <select class="uk-select" id="pay_or_booking" name="pay_or_booking">
                            <option>забронировано</option>
                            <option>оплачено</option>
                        </select>
                    </div>
                    <div id="booking_sum_div" class="uk-margin-small-top">
                        <input class="uk-input" id="booking_sum" type="number" name="booking_sum" value="" placeholder="Сумма к оплате при бронировании" autocomplete="off" required>
                    </div>
                    
                    <div class="uk-margin-small-top">
                        <select class="uk-select" id="confirm" name="confirm">
                            <option>явился</option>
                            <option>не явился</option>
                        </select>
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Внести правки</button>
                    </div>
                </form>
            </div>
            <br>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <h3 class="uk-margin-remove uk-card-title">Освободить место</h3>
                <p class="uk-text-warning uk-text-bold uk-text-center">Внимание! Операция освобождения места безвозвратна, все данные по выбранному месту будут удалены и место освободится.</p>
                <form class="uk-flex uk-flex-column" id="delete_ticket" action="/pravka-bileta-udalenie/" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_selected_bus" type="text" name="del_selected_bus" value="<?php echo $selected_bus; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_selected_id_bus" type="text" name="del_selected_id_bus" value="<?php echo $selected_id_bus; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_selected_date" type="text" name="del_selected_date" value="<?php echo $selected_date; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_selected_time" type="text" name="del_selected_time" value="<?php echo $selected_time; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_selected_seat" type="text" name="del_selected_seat" value="<?php echo $selected_seat; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_id_seat" type="text" name="del_id_seat" value="<?php echo $id_seat; ?>">
                    </div>
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="del_passenger" type="text" name="del_passenger" value="<?php echo $ticket->passenger; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Освободить место</button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>

<?php   
}
?>