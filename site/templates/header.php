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
                <a class="uk-margin-small uk-button uk-button-default" href="/registratciia-bileta-vybor-reisa/">Зарегистрировать билет</a>
                <a class="uk-margin-small uk-button uk-button-default" href="/pravka-bileta-vybor-reisa/">Правка билета</a>
                <a class="uk-margin-small uk-button uk-button-default" href="/reestr-passazhirov-vybor-passazhira/">Реестр пассажиров</a>
            </div>
        </div>

        <a href="/"><i class="fa-solid fa-house"></i></a>
        <p class="uk-margin-remove uk-text-bold ">Оператор: ' . $operator . '</p>
        <a href="/login/?logout" title="Выход из системы"><i class="fa-solid fa-right-from-bracket"></i></i></a>
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
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.15.18/dist/css/uikit.min.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $config->urls->templates; ?>styles/main.css">
        
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.2/css/all.css">
        
        <script src="https://cdn.jsdelivr.net/npm/uikit@3.15.18/dist/js/uikit.min.js"></script>
    </head>
    <body>
        
        
        
        
    <div class="uk-container">
        <nav class="uk-navbar-container uk-padding-small" uk-navbar>
            <?php echo $menu; ?>
        </nav>
    </div>