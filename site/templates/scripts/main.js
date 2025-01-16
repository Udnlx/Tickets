$(".readonly").keydown(function(e){
    e.preventDefault();
});



// $(document).keydown(function (event) {
//     if (event.keyCode == 123) { // Prevent F12
//         return false;
//     } else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) { // Prevent Ctrl+Shift+I        
//         return false;
//     }
// });



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
    let scrollTop = $('#filter_passenger').offset().top;
    $(document).scrollTop(scrollTop);
    $('#search_passenger').focus();
    $('#agent_search_passenger').focus();
});



//Скрипты Выбор мест для резерва билетов
$('button.uk-mass-reserv-seat').click(function() {
    if ($(this).hasClass('seat_select_mass')) {
        $(this).removeClass('seat_select_mass');
    } else {
        $(this).addClass('seat_select_mass');
    }
});

$('#reserv_seat-btn').hover(function() {
    $('#select_reserv_seat').val('');
    $('div.buttons_seat').find('button.seat_select_mass').each(function (){
        let selected_seat = $(this).text();
        console.log(selected_seat);
        $('#select_reserv_seat').val($('#select_reserv_seat').val() + selected_seat + ',');
    })
});
//Скрипты Выбор мест для резерва билетов

//Запись в базу о резерве билетов
$('#reserv_seat-btn').click(function() {
    var selected_bus = $('#selected_bus').val();
    var selected_id_bus = $('#selected_id_bus').val();
    var selected_date = $('#selected_date').val();
    var selected_time = $('#selected_time').val();
    var select_reserv_seat = $('#select_reserv_seat').val();
$.ajax({
    type: "POST",
    url: '/add_reserv_seats.php',
    data: {
        'selected_bus':selected_bus, 
        'selected_id_bus':selected_id_bus,
        'selected_date':selected_date,
        'selected_time':selected_time,
        'select_reserv_seat':select_reserv_seat,
    },
    beforeSend: function () {
        $('#seat_messages').html('<p class="messages" style="color: green;">Отправка и обработка данных...</p>');
    },
    success: function (data) {
        $('#seat_messages').html(data);
    },
    error: function (jqXHR, text, error) {
        $('#messages').html(error);
    }
});
return false;    
});
//Запись в базу о резерве билетов



//Скрипты Выбор мест для резерва билетов для агентов
$('button.uk-mass-reserv-seat-agent').click(function() {
    if ($(this).hasClass('agent-seat_select_mass')) {
        $(this).removeClass('agent-seat_select_mass');
    } else {
        $(this).addClass('agent-seat_select_mass');
    }
});

$('#agent-reserv_seat-btn').hover(function() {
    $('#select_reserv_seat').val('');
    $('div.buttons_seat').find('button.agent-seat_select_mass').each(function (){
        let selected_seat = $(this).text();
        console.log(selected_seat);
        $('#select_reserv_seat').val($('#select_reserv_seat').val() + selected_seat + ',');
    })
});
//Скрипты Выбор мест для резерва билетов для агентов

//Запись в базу о резерве билетов для агентов
$('#agent-reserv_seat-btn').click(function() {
    console.log ('asd');
    var selected_bus = $('#selected_bus').val();
    var selected_id_bus = $('#selected_id_bus').val();
    var selected_date = $('#selected_date').val();
    var selected_time = $('#selected_time').val();
    var select_reserv_seat = $('#select_reserv_seat').val();
$.ajax({
    type: "POST",
    url: '/add_reserv_seats_agent.php',
    data: {
        'selected_bus':selected_bus, 
        'selected_id_bus':selected_id_bus,
        'selected_date':selected_date,
        'selected_time':selected_time,
        'select_reserv_seat':select_reserv_seat,
    },
    beforeSend: function () {
        $('#seat_messages').html('<p class="messages" style="color: green;">Отправка и обработка данных...</p>');
    },
    success: function (data) {
        $('#seat_messages').html(data);
    },
    error: function (jqXHR, text, error) {
        $('#messages').html(error);
    }
});
return false;    
});
//Запись в базу о резерве билетов для агентов



$('button.uk-ticket-edit-seat').click(function() {
    let edited_seat = $(this).text();
    $('#selected_seat').val(edited_seat);
    let id_edited_seat = $(this).attr('id');
    $('#id_seat').val(id_edited_seat);
    $('#print_ticket_id').val(id_edited_seat);
});



$('#pay_or_booking').change( function() {
    let pob_selected = $('#pay_or_booking option:selected').text();
    if (pob_selected == 'забронировано') {
        $('#booking_sum_div').removeClass('uk-hidden');
        $('#booking_sum').val('');
    } else {
        $('#booking_sum_div').addClass('uk-hidden');
        $('#booking_sum').val('0');
    }
});



$('#type_ticket').change( function() {
    let type_selected = $('#type_ticket option:selected').text();
    if (type_selected == 'взрослый') {
        $('#agent_ticket option:first').prop('selected', true);
        let sel_price = $('#sel_price').text();
        let receive_price = sel_price;
        console.log (receive_price);
        receive_price = Math.ceil(receive_price);
        receive_price = Math.round(receive_price/10)*10;
        $('#price_ticket').val(receive_price);
    }
    if (type_selected == 'детский') {
        $('#agent_ticket option:first').prop('selected', true);
        let sel_price = $('#sel_price').text();
        let receive_price = sel_price/2;
        console.log (receive_price);
        receive_price = Math.ceil(receive_price);
        receive_price = Math.round(receive_price/10)*10;
        $('#price_ticket').val(receive_price);
    }
});


$('#agent_ticket').change( function() {
    let type_selected = $('#agent_ticket option:selected').text();
    let rate = $('#agent_ticket option:selected').attr('rate');
    let diff = $('#agent_ticket option:selected').attr('diff');
    let type_ticket = $('#type_ticket option:selected').text();
    let sel_price = $('#sel_price').text();
    if (diff) {
        if (type_ticket == 'взрослый') {
            let receive_price = sel_price-diff;
            console.log (receive_price);
            receive_price = Math.ceil(receive_price);
            receive_price = Math.round(receive_price/10)*10;
            $('#price_ticket').val(receive_price);
        } else {
            let receive_price = (sel_price/2)-diff;
            console.log (receive_price);
            receive_price = Math.ceil(receive_price);
            receive_price = Math.round(receive_price/10)*10;
            $('#price_ticket').val(receive_price);
        }
    } else {
        if (type_ticket == 'взрослый') {
            let receive_price = sel_price*rate;
            console.log (receive_price);
            receive_price = Math.ceil(receive_price);
            receive_price = Math.round(receive_price/10)*10;
            $('#price_ticket').val(receive_price);
        } else {
            let receive_price = (sel_price/2)*rate;
            console.log (receive_price);
            receive_price = Math.ceil(receive_price);
            receive_price = Math.round(receive_price/10)*10;
            $('#price_ticket').val(receive_price);
        }
    }
});



// $('#agent_ticket').change( function() {
//     let type_selected = $('#agent_ticket option:selected').text();
//     if (type_selected == 'Олимп') {
//         //console.log('пустой');
//         let type_ticket = $('#type_ticket option:selected').text();
//         if (type_ticket == 'взрослый') {
//             $('#price_ticket').val('4000');
//         } else {
//             $('#price_ticket').val('2000');
//         }
//     } else {
//         //console.log('не пустой');
//         if (type_selected == 'Росбилет') {
//             let type_ticket = $('#type_ticket option:selected').text();
//             let curent_page = $('h1').text();
//             if (type_ticket == 'взрослый') {
//                 $('#price_ticket').val('3900');
//             } else {
//                 $('#price_ticket').val('1950');
//             }
//             if (curent_page == 'Правка билета') {
//                 $('#price_ticket').val('3900');
//             }
//         } else {
//             let type_ticket = $('#type_ticket option:selected').text();
//             let curent_page = $('h1').text();
//             if (type_ticket == 'взрослый') {
//                 $('#price_ticket').val('4000');
//             } else {
//                 $('#price_ticket').val('2000');
//             }  
//             if (curent_page == 'Правка билета') {
//                 $('#price_ticket').val('4000');
//             }
//         }
//     }
// });



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
    let count_travel = $(this).attr('count_travel');
    //console.log (id_passenger);
    //console.log(name_passenger);
    //console.log(type_doc_passenger + ' ' + num_doc_passenger + ' ' + param_doc_passenger);
    $('#selected_idpassenger').val(id_passenger);
    $('#selected_name').val(name_passenger);
    $('#selected_document').val(type_doc_passenger + ' ' + series_doc_passenger + ' ' + num_doc_passenger);
    
    $('#passenger_idpassenger').val(id_passenger);
    $('#passenger_name').val(name_passenger);
    $('#passenger_document').val(type_doc_passenger + ' ' + series_doc_passenger + ' ' + num_doc_passenger);

    //console.log ('Проверяем количество поездок пассажира');
    let departure_date = $('#departure_date').text();
    if (departure_date > 1735678800) {
        if ((count_travel + 1) % 3 == 0) {
            alert ('У пассажира намечается очередная третья поездка');
        }
    }
});



//Скрипты нажатия кнопок станций посадки и высадки
$(document).on('click', 'button.uk-ticket-button-station-start', function(){
    $('div.uk-ticket-button-station-items').find('button.uk-ticket-button-station-start').each(function (){
        $(this).removeClass('station_select');
    })
    $(this).addClass('station_select');
    let selected_station_start = $(this).text();
    let id_selected_station_start = $(this).attr('id');
    let param_selected_station_start = $(this).attr('param_btn');
    // console.log(selected_station_start);
    // console.log(id_selected_station_start);
    
    $('#selected_station_start').val(param_selected_station_start);
    $('#id_selected_station_start').val(id_selected_station_start);

    // Получение цены от двeх станций
    $('#type_ticket option:first').prop('selected', true);
    $('#agent_ticket option:first').prop('selected', true);
    let startStation = $('div.start-station').find('button.station_select').text();
    startStation = $.trim(startStation);
    let finishStation = $('div.finish-station').find('button.station_select').text();
    finishStation = $.trim(finishStation);
    let ticketPrice = $('[ss="'+startStation+'"][sf="'+finishStation+'"]').attr('tp');
    if (finishStation) {
        if (ticketPrice) {
            $('#sel_price').text(ticketPrice);
            $('#price_ticket').val(ticketPrice);
        } else {
            $('#sel_price').text('5000');
            $('#price_ticket').val('5000');
        }
    }
    // ===============================
});

$(document).on('click', 'button.uk-ticket-button-station-finish', function(){
    $('div.uk-ticket-button-station-items').find('button.uk-ticket-button-station-finish').each(function (){
        $(this).removeClass('station_select');
    })
    $(this).addClass('station_select');
    let selected_station_finish = $(this).text();
    let id_selected_station_finish = $(this).attr('id');
    let param_selected_station_finish = $(this).attr('param_btn');
    // console.log(selected_station_finish);
    // console.log(id_selected_station_finish);
    
    $('#selected_station_finish').val(param_selected_station_finish);
    $('#id_selected_station_finish').val(id_selected_station_finish);

    // Получение цены от двeх станций
    $('#type_ticket option:first').prop('selected', true);
    $('#agent_ticket option:first').prop('selected', true);
    let startStation = $('div.start-station').find('button.station_select').text();
    startStation = $.trim(startStation);
    let finishStation = $('div.finish-station').find('button.station_select').text();
    finishStation = $.trim(finishStation);
    let ticketPrice = $('[ss="'+startStation+'"][sf="'+finishStation+'"]').attr('tp');
    if (finishStation) {
        if (ticketPrice) {
            $('#sel_price').text(ticketPrice);
            $('#price_ticket').val(ticketPrice);
        } else {
            $('#sel_price').text('5000');
            $('#price_ticket').val('5000');
        }
    }
    // ===============================
});



$.extend($.expr[':'], {
  'containsi': function(elem, i, match, array)
  {
    return (elem.textContent || elem.innerText || '').toLowerCase()
    .indexOf((match[3] || "").toLowerCase()) >= 0;
  }
});

//ДИНАМИЧЕСКИЙ ПОИСК ПО ПАССАЖИРАМ
// $('#search_passenger').keyup(function(){
//     let search_value = $('#search_passenger').val();
    
//     $('p.passengers_item').hide();
//     $('p.passengers_item:containsi("'+search_value+'")').show();
// });
//ДИНАМИЧЕСКИЙ ПОИСК ПО ПАССАЖИРАМ



//СТАТИЧЕСКИЙ ПОИСК ПО ПАССАЖИРАМ
$('#filter-passenger-btn').click(function() {
    var search_passenger = $('#search_passenger').val();
    $.ajax({
        type: "POST",
        url: '/filter-passenger.php',
        data: {
            'search_passenger':search_passenger
        },
        beforeSend: function () {
            $('#result-filter-passenger').html('<p class="messages" style="color: green;">Пожалуйста ожидайте...</p>');
        },
        success: function (data) {
            $('#result-filter-passenger').html(data);
        },
        error: function (jqXHR, text, error) {
            $('#result-filter-passenger').html(error);
        }
    });
    return false;    
});

$('#search_passenger').on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        var search_passenger = $('#search_passenger').val();
        $.ajax({
            type: "POST",
            url: '/filter-passenger.php',
            data: {
                'search_passenger':search_passenger
            },
            beforeSend: function () {
                $('#result-filter-passenger').html('<p class="messages" style="color: green;">Пожалуйста ожидайте...</p>');
            },
            success: function (data) {
                $('#result-filter-passenger').html(data);
            },
            error: function (jqXHR, text, error) {
                $('#result-filter-passenger').html(error);
            }
        });
        return false;  
    }
});

$('#filter-passenger-doc-btn').click(function() {
    var search_passenger = $('#search_passenger_doc').val();
    $.ajax({
        type: "POST",
        url: '/filter-passenger-doc.php',
        data: {
            'search_passenger':search_passenger
        },
        beforeSend: function () {
            $('#result-filter-passenger').html('<p class="messages" style="color: green;">Пожалуйста ожидайте...</p>');
        },
        success: function (data) {
            $('#result-filter-passenger').html(data);
        },
        error: function (jqXHR, text, error) {
            $('#result-filter-passenger').html(error);
        }
    });
    return false;    
});

$('#search_passenger_doc').on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        var search_passenger = $('#search_passenger_doc').val();
        $.ajax({
            type: "POST",
            url: '/filter-passenger-doc.php',
            data: {
                'search_passenger':search_passenger
            },
            beforeSend: function () {
                $('#result-filter-passenger').html('<p class="messages" style="color: green;">Пожалуйста ожидайте...</p>');
            },
            success: function (data) {
                $('#result-filter-passenger').html(data);
            },
            error: function (jqXHR, text, error) {
                $('#result-filter-passenger').html(error);
            }
        });
        return false;  
    }
});

$('#filter-passenger-phone-btn').click(function() {
    var search_passenger = $('#search_passenger_phone').val();
    $.ajax({
        type: "POST",
        url: '/filter-passenger-phone.php',
        data: {
            'search_passenger':search_passenger
        },
        beforeSend: function () {
            $('#result-filter-passenger').html('<p class="messages" style="color: green;">Пожалуйста ожидайте...</p>');
        },
        success: function (data) {
            $('#result-filter-passenger').html(data);
        },
        error: function (jqXHR, text, error) {
            $('#result-filter-passenger').html(error);
        }
    });
    return false;    
});

$('#search_passenger_phone').on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        var search_passenger = $('#search_passenger_phone').val();
        $.ajax({
            type: "POST",
            url: '/filter-passenger-phone.php',
            data: {
                'search_passenger':search_passenger
            },
            beforeSend: function () {
                $('#result-filter-passenger').html('<p class="messages" style="color: green;">Пожалуйста ожидайте...</p>');
            },
            success: function (data) {
                $('#result-filter-passenger').html(data);
            },
            error: function (jqXHR, text, error) {
                $('#result-filter-passenger').html(error);
            }
        });
        return false;  
    }
});
//СТАТИЧЕСКИЙ ПОИСК ПО ПАССАЖИРАМ



//СТАТИЧЕСКИЙ ПОИСК ПО ПАССАЖИРАМ ДЛЯ АГЕНТОВ
$('#agent-filter-passenger-btn').click(function() {
    var search_passenger = $('#agent_search_passenger').val();
    $.ajax({
        type: "POST",
        url: '/agent-filter-passenger.php',
        data: {
            'search_passenger':search_passenger
        },
        beforeSend: function () {
            $('#result-filter-passenger').html('<p class="messages" style="color: green;">Пожалуйста ожидайте...</p>');
        },
        success: function (data) {
            $('#result-filter-passenger').html(data);
        },
        error: function (jqXHR, text, error) {
            $('#result-filter-passenger').html(error);
        }
    });
    return false;    
});

$('#agent_search_passenger').on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        var search_passenger = $('#agent_search_passenger').val();
        $.ajax({
            type: "POST",
            url: '/agent-filter-passenger.php',
            data: {
                'search_passenger':search_passenger
            },
            beforeSend: function () {
                $('#result-filter-passenger').html('<p class="messages" style="color: green;">Пожалуйста ожидайте...</p>');
            },
            success: function (data) {
                $('#result-filter-passenger').html(data);
            },
            error: function (jqXHR, text, error) {
                $('#result-filter-passenger').html(error);
            }
        });
        return false;  
    }
});

$('#agent-filter-passenger-doc-btn').click(function() {
    var search_passenger = $('#agent_search_passenger_doc').val();
    $.ajax({
        type: "POST",
        url: '/agent-filter-passenger-doc.php',
        data: {
            'search_passenger':search_passenger
        },
        beforeSend: function () {
            $('#result-filter-passenger').html('<p class="messages" style="color: green;">Пожалуйста ожидайте...</p>');
        },
        success: function (data) {
            $('#result-filter-passenger').html(data);
        },
        error: function (jqXHR, text, error) {
            $('#result-filter-passenger').html(error);
        }
    });
    return false;    
});

$('#agent_search_passenger_doc').on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        var search_passenger = $('#agent_search_passenger_doc').val();
        $.ajax({
            type: "POST",
            url: '/agent-filter-passenger-doc.php',
            data: {
                'search_passenger':search_passenger
            },
            beforeSend: function () {
                $('#result-filter-passenger').html('<p class="messages" style="color: green;">Пожалуйста ожидайте...</p>');
            },
            success: function (data) {
                $('#result-filter-passenger').html(data);
            },
            error: function (jqXHR, text, error) {
                $('#result-filter-passenger').html(error);
            }
        });
        return false;  
    }
});

$('#agent-filter-passenger-phone-btn').click(function() {
    var search_passenger = $('#agent_search_passenger_phone').val();
    $.ajax({
        type: "POST",
        url: '/agent-filter-passenger-phone.php',
        data: {
            'search_passenger':search_passenger
        },
        beforeSend: function () {
            $('#result-filter-passenger').html('<p class="messages" style="color: green;">Пожалуйста ожидайте...</p>');
        },
        success: function (data) {
            $('#result-filter-passenger').html(data);
        },
        error: function (jqXHR, text, error) {
            $('#result-filter-passenger').html(error);
        }
    });
    return false;    
});

$('#agent_search_passenger_phone').on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        var search_passenger = $('#agent_search_passenger_phone').val();
        $.ajax({
            type: "POST",
            url: '/agent-filter-passenger-phone.php',
            data: {
                'search_passenger':search_passenger
            },
            beforeSend: function () {
                $('#result-filter-passenger').html('<p class="messages" style="color: green;">Пожалуйста ожидайте...</p>');
            },
            success: function (data) {
                $('#result-filter-passenger').html(data);
            },
            error: function (jqXHR, text, error) {
                $('#result-filter-passenger').html(error);
            }
        });
        return false;  
    }
});
//СТАТИЧЕСКИЙ ПОИСК ПО ПАССАЖИРАМ ДЛЯ АГЕНТОВ



//ДИНАМИЧЕСКИЙ ПОИСК ПО БИЛЕТАМ
$('#search_tickets').keyup(function(){
    let search_value = $('#search_tickets').val();
    
    $('p.reestr_seat_item').hide();
    $('p.reestr_seat_item:containsi("'+search_value+'")').show();
});
//ДИНАМИЧЕСКИЙ ПОИСК ПО БИЛЕТАМ



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
    var add_agent = $('#agent').val();
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
        'add_phone_passenger':add_phone_passenger,
        'add_agent':add_agent
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
        $('#agent').val('');
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
//Добавление нового пассажира



//Добавление нового пассажира для агентов
$('#agent_add_passenger').click(function() {
    var add_name_passenger = $('#name_passenger').val();
    var add_birthday_passenger = $('#birthday_passenger').val();
    var day_rev = add_birthday_passenger.split("-").reverse().join(".");
    var add_birthday_passenger = day_rev;
    var add_type_doc_passenger = $('#type_doc_passenger').val();
    var add_num_doc_passenger = $('#num_doc_passenger').val();
    var add_passport_passenger = $('#passport_passenger').val();
    var add_phone_passenger = $('#phone_passenger').val();
    var add_agent = $('#agent').val();
    //console.log(add_name_passenger, add_birthday_passenger, add_type_doc_passenger, add_num_doc_passenger, add_passport_passenger, add_phone_passenger);
$.ajax({
    type: "POST",
    url: '/agent_add_new_passenger.php',
    data: {
        'add_name_passenger':add_name_passenger, 
        'add_birthday_passenger':add_birthday_passenger,
        'add_type_doc_passenger':add_type_doc_passenger,
        'add_num_doc_passenger':add_num_doc_passenger,
        'add_passport_passenger':add_passport_passenger,
        'add_phone_passenger':add_phone_passenger,
        'add_agent':add_agent
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
        $('#agent').val('');
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
//Добавление нового пассажира для агентов



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
    
    var old_name_passenger = $('#old_name_passenger').val();
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
        
        'old_name_passenger':old_name_passenger, 
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
//Редактирование пассажира



//Проверка свободно ли место перед регистрацией билета
$('#select_seat').submit(function(){
    if(!$(this).attr('validated')) {
    console.log ('делаем проверку места и дубликат пассажира');
    var check_selected_bus = $('#selected_bus').val();
    var check_selected_id_bus = $('#selected_id_bus').val();
    var check_selected_date = $('#selected_date').val();
    var check_selected_time = $('#selected_time').val();
    var check_selected_seat = $('#selected_seat').val();
    var check_selected_name = $('#selected_name').val();
    
    $.ajax({
        type: "POST",
        url: '/check_seat.php',
        data: {
            'check_selected_bus':check_selected_bus, 
            'check_selected_id_bus':check_selected_id_bus,
            'check_selected_date':check_selected_date,
            'check_selected_time':check_selected_time,
            'check_selected_seat':check_selected_seat,
            'check_selected_name':check_selected_name
        },
        beforeSend: function () {
            $('#seat_messages').html('<p class="messages" style="color: green;">Идет проверка места...</p>');
        },
        success: function (data) {
            $('#seat_messages').html(data);
            let message = $('#seat_messages p.messages').text();
            let warning = $('#seat_messages p.warning').text();
            console.log (message);
            console.log (warning);
            if (message == 'Проверка места пройдена, найден дубликат пассажира') {
                alert(warning);
                console.log ('РЕГЕСТРИРУЕМ');
                $('#select_seat').attr('validated',true);
                $('#select_seat').submit();
            } else {
                console.log ('НЕ РЕГЕСТРИРУЕМ');
            }
            if (message == 'Проверка места пройдена, регистрируем') {
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
//Проверка свободно ли место перед регистрацией билета