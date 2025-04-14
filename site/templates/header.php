<?php namespace ProcessWire;

if(isset($_SESSION['operator'])){
    $operator = $_SESSION['operator'];
} else {
    $operator = 'no_operator';
}

$url = $_SERVER['REQUEST_URI'];
$url = explode('?', $url);
$url = $url[0];
//echo $url;
$menu = '
    <div class="uk-navbar-left">
        <a href="#offcanvas-usage" uk-toggle><i class="fa-solid fa-bars"></i></a>

        <div id="offcanvas-usage" uk-offcanvas>
            <div class="uk-offcanvas-bar">
                <button class="uk-offcanvas-close" type="button" uk-close></button>
                <br>
                <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя страница</a>
                <br><br>
                <div class="uk-flex-column">
                    <div class="uk-flex uk-flex-middle">
                        <button class="demo-seat uk-button" disabled>00</button>
                        <p class="uk-margin-remove">-</p>
                        <p class="legend-title">Место свободное</p>
                    </div>
                    <div class="uk-flex uk-flex-middle">
                        <button class="demo-seat uk-button" disabled>00<p class="sb_occupied"></p></button>
                        <p class="uk-margin-remove">-</p>
                        <p class="legend-title">Место свободное,<br>синхрон с 1С</p>
                    </div>
                    <div class="uk-flex uk-flex-middle">
                        <button class="demo-seat uk-button seat_reserv" disabled>00<p class="appeared"></p></button>
                        <p class="uk-margin-remove">-</p>
                        <p class="legend-title">Место забронировано пассажиром</p>
                    </div>
                    <div class="uk-flex uk-flex-middle">
                        <button class="demo-seat uk-button seat_reserv" disabled>00<p class="noappeared"><i class="fa-solid fa-triangle-exclamation"></i></p></button>
                        <p class="uk-margin-remove">-</p>
                        <p class="legend-title">Место забронировано,<br>не подтвержденно</p>
                    </div>
                    <div class="uk-flex uk-flex-middle">
                        <button class="demo-seat uk-button seat_pay" disabled>00</button>
                        <p class="uk-margin-remove">-</p>
                        <p class="legend-title">Место оплаченно</p>
                    </div>
                    <div class="uk-flex uk-flex-middle">
                        <button class="demo-seat uk-button seat_pay" disabled>00<p class="noappeared">API</p></button>
                        <p class="uk-margin-remove">-</p>
                        <p class="legend-title">Место оплаченно,<br>купленно через API систему</p>
                    </div>
                    <div class="uk-flex uk-flex-middle">
                        <button class="demo-seat uk-button seat_select_mass" disabled>00</button>
                        <p class="uk-margin-remove">-</p>
                        <p class="legend-title">Место забронировано оператором</p>
                    </div>
                    <div class="uk-flex uk-flex-middle">
                        <button class="demo-seat uk-button seat_select_mass_agent" disabled>00</button>
                        <p class="uk-margin-remove">-</p>
                        <p class="legend-title">Место забронировано агентом</p>
                    </div>
                </div>
            </div>
        </div>

        <a href="/"><i class="fa-solid fa-house"></i></a>
        <p class="uk-margin-remove uk-text-bold ">Оператор: ' . $operator . '</p>
        <a href="/login/?logout" title="Выход из системы"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>
   ';
if ($url == '/login/') {
   $menu = '';
}

?>

    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tickets</title>
        <meta name="Description" content="Программа для ведения учета заполняемости автобусов">
        
        <link rel="stylesheet" href="<?php echo $config->urls->templates; ?>styles/uikit.min.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $config->urls->templates; ?>styles/main.css?v=<?php echo uniqid(); ?>">
        
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.2/css/all.css">
        
        <script src="<?php echo $config->urls->templates; ?>scripts/uikit.min.js"></script>
    </head>
    <body>
        
        
        
        
    <div class="uk-container">
        <nav class="uk-navbar-container uk-padding-small" uk-navbar>
            <?php echo $menu; ?>
        </nav>
    </div>