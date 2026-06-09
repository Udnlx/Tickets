<?php namespace ProcessWire;

error_reporting(E_ERROR | E_PARSE);

$access = '';
if(isset($_SESSION['access'])){
    $access = $_SESSION['access'];
}
 
$selected_id_ticket = !empty($_POST['post_id_ticket'])?$_POST['post_id_ticket']:NULL;

$ticket_pages = $pages->find('template=purchased_tickets, id=' . $selected_id_ticket . '');

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

if ($operator == 'no_operator' || $access == 'agent') {
?>

<div id="content" style="max-width: 700px;">
	<h1 class="uk-heading-hero uk-text-center">Найденный билет по ID</h1>
	
	            
    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
        <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
        <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
    </div>
</div>

<?php    
} else {
?>

<?php
$find_tickets = '';
if ($ticket_pages) {
    $find_tickets = '';
    foreach ($ticket_pages as $ticket_itm) {
        $find_tickets .= '
            <button id="' . $ticket_itm->id . '" class="uk-ticket-edit-id uk-margin-small-top uk-button uk-button-default">
                <p id="ticket_id" class="uk-margin-remove">' . $ticket_itm->id . '</p>
                <p class="uk-margin-remove">' . $ticket_itm->title . '</p>
                <p class="uk-margin-remove">' . $ticket_itm->passenger . '</p>
                <p class="uk-margin-remove">' . $ticket_itm->price_ticket . '</p>
            </button>
        ';
    }
} else {
    $find_tickets = 'Билеты не найдены';
}
?>

<div id="content">
	<h1 class="uk-heading-hero uk-text-center">Найденный билет по ID</h1>
	<div class="uk-child-width-1-2@m" uk-grid>
	    
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column" uk-sticky>
                <p class="operator uk-position-absolute">Оператор: <?php echo $operator; ?></p>
                <form class="uk-flex uk-flex-column" id="select_edit_seat" action="/pravka-bileta-forma/" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input" id="sb_idbus" type="text" name="sb_idbus" value="<?php echo $uid; ?>">
                    </div>
                    
                    <div class="uk-margin-small-top">
                        <input class="uk-input readonly" id="id_seat" type="text" name="id_seat" value="" placeholder="ID билета" autocomplete="off" required>
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Править</button>
                        <a class="uk-margin-small uk-button uk-button-default" href="/pravka-bileta-vybor-reisa/">К выбору рейса</a>
                    </div>
                </form>

                <form class="uk-flex uk-flex-column" id="print_ticket" action="/pechat-bileta/" method="post">
                    <div class="uk-margin-small-top uk-hidden">
                        <input class="uk-input readonly" id="print_ticket_id" type="text" name="print_ticket_id" value="" placeholder="ID билета" autocomplete="off" required>
                    </div>
                    
                    <div class="uk-margin-small-top uk-flex uk-flex-column">
                        <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Распечатать билет</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-flex uk-flex-column">
                <?php echo $find_tickets ; ?>
            </div>
        </div>
        
    </div>
</div>





<?php   
}
?>