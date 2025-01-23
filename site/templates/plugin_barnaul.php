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
    	<h1 class="uk-heading-hero uk-text-center">Плагин Барнаул</h1>          
        <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
            <h3 class="uk-card-title">Сессия потеряна, перезайти</h3>
            <a class="uk-margin-small uk-button uk-button-default" href="/login/">Перезайти</a>
        </div>
    </div>
<?php    
} else {
    if ($access == 'admin') {
    ?>
        <div id="content">
            <h1 class="uk-heading-hero uk-text-center">Плагин Барнаул</h1>
                  
            <div id=app></div>
            <div class="uk-margin-small-top uk-flex uk-flex-column">
            	<a class="uk-margin-small uk-button uk-button-default" href="/">Назад</a>
            </div>
	        <script>
				const divApp = document.getElementById('app');
				window.token = "r6f2u5-xg6q6t-7ke8r8-knlnv4-sqb6md";
				fetch("https://avtobus.online/api/widget/version/" + window.token).then(function(response) {
					response.json().then(function(json) {
					const script = document.createElement("script");
					script.src = json.script;

					const pre_script = document.createElement("script");
					pre_script.src = json.url;
					divApp.appendChild(pre_script);
					divApp.appendChild(script)
					});
				});
			</script>
        </div>
    <?php
    } else {
    ?>
        <div id="content" style="max-width: 700px;">
            <h1 class="uk-heading-hero uk-text-center">Плагин Барнаул<h1>
            <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-flex uk-flex-column">
                <h3 class="uk-card-title">К этой странице у Вас нет доступа</h3>
                <a class="uk-margin-small uk-button uk-button-default" href="/">Домашняя</a>
            </div>
        </div>
    <?php
    }
}
?>