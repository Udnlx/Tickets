<?php namespace ProcessWire;

//Таблица всех станций посадки
$table_station = '';
$all_bus = $pages->find('template=buses_item, sort=sort');
foreach ($all_bus as $bus) {
    foreach ($bus->station_start as $station) {
        $array = preg_split('/[—]/u', $station->title, -1, PREG_SPLIT_NO_EMPTY);
        $station_name = $array[0];
        $station_time = substr ($array[0], -6);
        $station_time = preg_replace('/\s+/', '', $station_time);
        //echo $bus->title . ' - ' . $station_name . ' - ' . $station_time . '<br>';
        $table_station .= '
        <p class="table_station" bus_name="' . $bus->title . '" station_name="' . $station_name . '" station_time="' . $station_time . '">' . $bus->title . ' - ' . $station_name . '</p>
        ';
    }
}

?>

    <div class="uk-hidden">
        <div class="table_station_block">
           <?php echo $table_station; ?> 
        </div>
    </div>

    <!-- Модальное окно добавления пола пассажира-->
    <div id="modal-add_gender" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <p style="font-weight: 700;text-align:center">У пассажира не хватает данных для регистрации.<br>Выберите пожалуйста пол и гражданство для этого пассажира.</p>
            <div id="messages_add_gender" class="messages-block">
                <p class="messages" style="color: green;"></p>
            </div>
  
            <div class="uk-margin-small-top">
                <label for="footer_gender_passenger">Пол</label>
                <select class="uk-select" id="footer_gender_passenger" name="footer_gender_passenger" required>
                    <option value="">Выберите пол</option>
                    <option value="М">М</option>
                    <option value="Ж">Ж</option>
                </select>
            </div>
            <div class="uk-margin-small-top">
                <label for="gender_passenger">Гражданство</label>
                <select class="uk-select" id="footer_citizenship_passenger" name="footer_citizenship_passenger" required>
                    <option value="">Выберите гражданство</option>
                    <option value="RU">Россия</option>
                    <option value="TJ">Таджикистан</option>
                    <option value="UZ">Узбекистан</option>
                    <option value="KG">Киргизия</option>
                    <option value="KZ">Казахстан</option>
                    <option value="BY">Беларусь</option>
                    <option value="UA">Украина</option>
                </select>
            </div>
            <br>
            <div class="uk-flex uk-flex-center">
                <button id="footer_add_gender" class="uk-button uk-button-primary" type="button">Добавить</button>
            </div>
        </div>
    </div>


    
    <div class="uk-padding-small uk-container uk-flex uk-flex-column uk-flex-middle">
        <h2 class="uk-margin-remove uk-heading-small">Tickets</h2>
        <p class="uk-margin-remove uk-text-small uk-text-center">Программа для ведения учета заполняемости автобусов</p>
        <p class="uk-margin-remove uk-text-small uk-text-center">© 2023-<?php echo date("Y"); ?> NikiDa (www.nikida.ru)</p>
    </div>
    
    
    
    
    
    <script src="<?php echo $config->urls->templates; ?>scripts/jquery-3.5.1.min.js"></script>
    <script src="<?php echo $config->urls->templates; ?>scripts/main.js?v=<?php echo uniqid(); ?>"></script>
    <script src="<?php echo $config->urls->templates; ?>scripts/jquery.cookie.js"></script>

    <script>
        const tm = () => {
        var Data = new Date();
        var Hour = Data.getHours().toString().padStart(2, '0');
        var Minutes = (Data.getMinutes()+10).toString().padStart(2, '0');
        if (Minutes >= '60') {
            var Hour = (Data.getHours()+1).toString().padStart(2, '0');
            var Minutes = (Data.getMinutes()-50).toString().padStart(2, '0');
        }
        let time = Hour + '-' + Minutes;
        console.log("Текущее время: "+ time);

        let station_string = '';
        $('div.table_station_block').find('p.table_station').each(function (){
            let station_time = $(this).attr('station_time');
            if (time == station_time) {
                let bus_name = $(this).attr('bus_name');
                let station_name = $(this).attr('station_name');
                let station_itm = bus_name + ' - ' + station_name;
                station_string = station_string + station_itm + '\n';
            }
        })
        //console.log (station_string);
        if (station_string && $.cookie('alertTrack') != 'view') {
            alert('Через 10 минут будут отправления со следующих станций:\n' + station_string);
            var date = new Date();
            date.setTime(date.getTime() + (1000 * 60));
            $.cookie("alertTrack", "view", { expires: date, path: '/' });
        }

        setTimeout(tm, 1000 * 60);
        }
        tm()
    </script>
    
    
    
    
    
    </body>
    </html>