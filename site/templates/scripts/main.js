$(".readonly").keydown(function(e){
    e.preventDefault();
});



$('button.uk-ticket-button').click(function() {
    let selected_bus = $(this).html();
    let id_bus = $(this).attr('id');
    let selected_busArr = selected_bus.split('<br>');
    let name_selected_bus = selected_busArr[0];
    let option_selected_bus = selected_busArr[1];
    option_selected_bus = option_selected_bus.replace('<span>', '');
    option_selected_bus = option_selected_bus.replace('</span>', '');
    let time = option_selected_bus.split('Отправление');
    let time_start = time[1];
    $('#post_bus').val(name_selected_bus);
    $('#option_bus').text(option_selected_bus);
    $('#post_time').val(time_start);
    $('#post_id_bus').val(id_bus);
});



$('button.uk-ticket-seat').click(function() {
    $('div.buttons_seat').find('button.seat_free').each(function (){
        $(this).removeClass('seat_select');
    })
    $(this).addClass('seat_select');
    let selected_seat = $(this).text();
    $('#selected_seat').val(selected_seat);
    let scrollTop = $('#search_passenger').offset().top;
    $(document).scrollTop(scrollTop);
    $('#search_passenger').focus();
});



$('button.uk-ticket-edit-seat').click(function() {
    let edited_seat = $(this).text();
    $('#selected_seat').val(edited_seat);
    let id_edited_seat = $(this).attr('id');
    $('#id_seat').val(id_edited_seat);
    $('#print_ticket_id').val(id_edited_seat);
});



//$('p.passengers_item').click(function() {
$(document).on('click', 'p.passengers_item', function(){
    $('div.reestr_passenger').find('p.passengers_item').each(function (){
        $(this).removeClass('passenger_select');
    })
    $(this).addClass('passenger_select');
    let passenger = $(this).html();
    let passenger_arr = passenger.split('<br>');
    let name_passenger = passenger_arr[0];
    let passenger_doc_arr = passenger_arr[1].split(' — ');
    let type_doc_passenger = passenger_doc_arr[1];
    let series_doc_passenger = passenger_doc_arr[2];
    let num_doc_passenger = passenger_doc_arr[3];
    let id_passenger = $(this).attr('id');
    //console.log (id_passenger);
    //console.log(name_passenger);
    //console.log(type_doc_passenger + ' ' + num_doc_passenger + ' ' + param_doc_passenger);
    $('#selected_idpassenger').val(id_passenger);
    $('#selected_name').val(name_passenger);
    $('#selected_document').val(type_doc_passenger + ' ' + series_doc_passenger + ' ' + num_doc_passenger);
    
    $('#passenger_idpassenger').val(id_passenger);
    $('#passenger_name').val(name_passenger);
    $('#passenger_document').val(type_doc_passenger + ' ' + series_doc_passenger + ' ' + num_doc_passenger);
});



$(document).on('click', 'button.uk-ticket-button-station', function(){
    $('div.uk-ticket-button-station-items').find('button.uk-ticket-button-station').each(function (){
        $(this).removeClass('station_select');
    })
    $(this).addClass('station_select');
    let selected_station = $(this).text();
    let id_selected_station = $(this).attr('id');
    // console.log(selected_station);
    // console.log(id_selected_station);
    
    $('#selected_station').val(selected_station);
    $('#id_selected_station').val(id_selected_station);
});



$.extend($.expr[':'], {
  'containsi': function(elem, i, match, array)
  {
    return (elem.textContent || elem.innerText || '').toLowerCase()
    .indexOf((match[3] || "").toLowerCase()) >= 0;
  }
});

$('#search_passenger').keyup(function(){
    let search_value = $('#search_passenger').val();
    
    $('p.passengers_item').hide();
    $('p.passengers_item:containsi("'+search_value+'")').show();
});

$('#search_tickets').keyup(function(){
    let search_value = $('#search_tickets').val();
    
    $('p.reestr_seat_item').hide();
    $('p.reestr_seat_item:containsi("'+search_value+'")').show();
});



//Добавление нового пассажира
$('#add_passenger').click(function() {
    var add_name_passenger = $('#name_passenger').val();
    var add_birthday_passenger = $('#birthday_passenger').val();
    var day_rev = add_birthday_passenger.split("-").reverse().join(".");
    var add_birthday_passenger = day_rev;
    var add_type_doc_passenger = $('#type_doc_passenger').val();
    var add_num_doc_passenger = $('#num_doc_passenger').val();
    var add_passport_passenger = $('#passport_passenger').val();
    var add_phone_passenger = $('#phone_passenger').val();
    //console.log(add_name_passenger, add_birthday_passenger, add_type_doc_passenger, add_num_doc_passenger, add_passport_passenger, add_phone_passenger);
$.ajax({
    type: "POST",
    url: '/add_new_passenger.php',
    data: {
        'add_name_passenger':add_name_passenger, 
        'add_birthday_passenger':add_birthday_passenger,
        'add_type_doc_passenger':add_type_doc_passenger,
        'add_num_doc_passenger':add_num_doc_passenger,
        'add_passport_passenger':add_passport_passenger,
        'add_phone_passenger':add_phone_passenger
    },
    beforeSend: function () {
        $('#messages').html('<p class="messages" style="color: green;">Отправка и обработка данных...</p>');
    },
    success: function (data) {
        $('#messages').html(data);
        
        let v = $('p.messages').text();
        if (v.indexOf('Ошибка') == -1) {
        $('#name_passenger').val('');
        $('#birthday_passenger').val('');
        $('#type_doc_passenger').val('');
        $('#num_doc_passenger').val('');
        $('#passport_passenger').val('');
        $('#phone_passenger').val('');
        let allpassengers = $('#get_all_passengers').html();
        $('#search_passenger').val('');
        $('.reestr_passenger').html(allpassengers);
        }
    },
    error: function (jqXHR, text, error) {
        $('#messages').html(error);
    }
});
return false;    
});



//Редактирование пассажира
$('#edit_passenger').click(function() {
    var id_passenger = $('#id_passenger').val();
    var edit_name_passenger = $('#name_passenger').val();
    var edit_birthday_passenger = $('#birthday_passenger').val();
    var day_rev = edit_birthday_passenger.split("-").reverse().join(".");
    var edit_birthday_passenger = day_rev;
    var edit_type_doc_passenger = $('#type_doc_passenger').val();
    var edit_num_doc_passenger = $('#num_doc_passenger').val();
    var edit_passport_passenger = $('#passport_passenger').val();
    var edit_phone_passenger = $('#phone_passenger').val();
    
    var old_birthday_passenger = $('#old_birthday_passenger').val();
    var old_day_rev = old_birthday_passenger.split("-").reverse().join(".");
    var old_birthday_passenger = old_day_rev;
    var old_type_doc_passenger = $('#old_type_doc_passenger').val();
    var old_num_doc_passenger = $('#old_num_doc_passenger').val();
    var old_passport_passenger = $('#old_passport_passenger').val();
    var old_phone_passenger = $('#old_phone_passenger').val();
    //console.log(id_passenger, edit_name_passenger, edit_birthday_passenger, edit_type_doc_passenger, edit_num_doc_passenger, edit_passport_passenger, edit_phone_passenger);
$.ajax({
    type: "POST",
    url: '/edit_passenger.php',
    data: {
        'id_passenger':id_passenger, 
        'edit_name_passenger':edit_name_passenger, 
        'edit_birthday_passenger':edit_birthday_passenger,
        'edit_type_doc_passenger':edit_type_doc_passenger,
        'edit_num_doc_passenger':edit_num_doc_passenger,
        'edit_passport_passenger':edit_passport_passenger,
        'edit_phone_passenger':edit_phone_passenger,
        
        'old_birthday_passenger':old_birthday_passenger,
        'old_type_doc_passenger':old_type_doc_passenger,
        'old_num_doc_passenger':old_num_doc_passenger,
        'old_passport_passenger':old_passport_passenger,
        'old_phone_passenger':old_phone_passenger
    },
    beforeSend: function () {
        $('#edit_messages').html('<p class="messages" style="color: green;">Отправка и обработка данных...</p>');
    },
    success: function (data) {
        $('#edit_messages').html(data);
        window.setTimeout('window.location.replace("/reestr-passazhirov-vybor-passazhira/");', 2000);
    },
    error: function (jqXHR, text, error) {
        $('#edit_messages').html(error);
    }
});
return false;    
});



//Проверка свободно ли место перед регистрацией билета
$('#select_seat').submit(function(){
    if(!$(this).attr('validated')) {
    console.log ('делаем проверку места');
    var check_selected_bus = $('#selected_bus').val();
    var check_selected_id_bus = $('#selected_id_bus').val();
    var check_selected_date = $('#selected_date').val();
    var check_selected_time = $('#selected_time').val();
    var check_selected_seat = $('#selected_seat').val();
    
$.ajax({
    type: "POST",
    url: '/check_seat.php',
    data: {
        'check_selected_bus':check_selected_bus, 
        'check_selected_id_bus':check_selected_id_bus,
        'check_selected_date':check_selected_date,
        'check_selected_time':check_selected_time,
        'check_selected_seat':check_selected_seat
    },
    beforeSend: function () {
        $('#seat_messages').html('<p class="messages" style="color: green;">Идет проверка места...</p>');
    },
    success: function (data) {
        $('#seat_messages').html(data);
        let message = $('#seat_messages p.messages').text();
        console.log (message);
        if (message == 'Место свободно, регистрируем') {
            console.log ('РЕГЕСТРИРУЕМ');
            $('#select_seat').attr('validated',true);
            $('#select_seat').submit();
        } else {
            console.log ('НЕ РЕГЕСТРИРУЕМ');
        }
    },
    error: function (jqXHR, text, error) {
        $('#seat_messages').html(error);
    }
});
    return false;  
    }                            
    return true;
});