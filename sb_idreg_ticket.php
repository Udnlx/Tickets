<?php

namespace ProcessWire;

require_once 'index.php';

$id_edit_ticket = $_POST['id_edit_ticket'];
$sb_id_ticket = $_POST['sb_id_ticket'];

//Записываем 1С ID билета в билет
$edit_page = $pages->get('template=purchased_tickets, id=' . $id_edit_ticket . '');
$edit_page->of(false);
$edit_page->sb_ticket_id = $sb_id_ticket;
$edit_page->save();
//Записываем 1С ID билета в билет