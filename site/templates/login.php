<?php namespace ProcessWire;

if(isset($_GET['logout'])) {
    session_unset();
}

$login = 'off';
$_SESSION['operator'] = 'no_operator';
//echo $_SESSION['operator'];

$user_login = !empty($_POST['user_login'])?$_POST['user_login']:NULL;  
$user_password = !empty($_POST['user_password'])?$_POST['user_password']:NULL;

if($user_login === 'admin-test' && $user_password === 'Pd9rMhj') {
    $login = 'on';
    $_SESSION['operator'] = 'admin-test';
    $_SESSION['access'] = 'admin';
}
if($user_login === 'Директор' && $user_password === 'K2KC87s') {
    $login = 'on';
    $_SESSION['operator'] = 'Директор';
    $_SESSION['access'] = 'manager';
}
if($user_login === 'Люда' && $user_password === 'Fm6krwB') {
    $login = 'on';
    $_SESSION['operator'] = 'Люда';
    $_SESSION['access'] = 'manager';
}
if($user_login === 'Ира' && $user_password === 'nXA7z52') {
    $login = 'on';
    $_SESSION['operator'] = 'Ира';
    $_SESSION['access'] = 'manager';
}
if($user_login === 'Юля' && $user_password === 'bMefP14') {
    $login = 'on';
    $_SESSION['operator'] = 'Юля';
    $_SESSION['access'] = 'manager';
}
if($user_login === 'Алла' && $user_password === 'QXFp8AH') {
    $login = 'on';
    $_SESSION['operator'] = 'Алла';
    $_SESSION['access'] = 'manager';
}
if($user_login === 'Оверченко' && $user_password === 'JggQB9P') {
    $login = 'on';
    $_SESSION['operator'] = 'Оверченко';
    $_SESSION['access'] = 'manager';
}
if($user_login === 'Ира2' && $user_password === 'uJL75Nc') {
    $login = 'on';
    $_SESSION['operator'] = 'Ира2';
    $_SESSION['access'] = 'operator';
}
if($user_login === 'Дмитрий' && $user_password === '262dDbj') {
    $login = 'on';
    $_SESSION['operator'] = 'Дмитрий';
    $_SESSION['access'] = 'operator';
}
if($user_login === 'Анна' && $user_password === 'G4WeqjA') {
    $login = 'on';
    $_SESSION['operator'] = 'Анна';
    $_SESSION['access'] = 'operator';
}

//echo $login;

$content = '';
if ($login == 'on') {
    $content = '
    <div id="content" style="max-width: 700px;">
    	<h1 class="uk-heading-hero uk-text-center">Добро пожаловать '. $_SESSION['operator'] .'</h1>
    	
    	<div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
            <div class="uk-margin-small-top uk-flex uk-flex-column">
                <a class="uk-margin-small-top uk-button uk-button-default" href="/">Перейти на главную</a>
                <a class="uk-margin-small-top uk-button uk-button-default" href="?logout">Выход</a>
            </div>
        </div>
    </div>
    ';
} else {
    $content = '
    <div id="content" style="max-width: 700px;">
    	<h1 class="uk-heading-hero uk-text-center">Вход</h1>
    	
    	            
        <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
            <form class="uk-flex uk-flex-column" id="select_bus" action="/login/" method="post">
                <div class="uk-margin-small-top">
                    <input class="uk-input" id="user_login" type="text" name="user_login" placeholder="Логин" required>
                </div>
                <div class="uk-margin-small-top">
                    <input class="uk-input" id="user_password" type="password" name="user_password" placeholder="Пароль" required>
                </div>
                
                <div class="uk-margin-small-top uk-flex uk-flex-column">
                <button class="uk-margin-small-top uk-button uk-button-default" type="submit">Войти</button>
                </div>
            </form>
        </div>
    </div>
    ';
}

echo $content;

?>