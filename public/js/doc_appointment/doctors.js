$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var selectedService, selectedDoctor, selectedDay, selectedSlot, selectedTimestamp;

    $(document).on('click', '.service, .doctor-services>div', function () {
        $('.is-selected').removeClass('is-selected');
        $('.calendar, .schedule').hide();
        loadDoctors($(this).data('id'));
        $('.service[data-id="'+$(this).data('id')+'"]').addClass('is-selected');
    });

    $(document).on('click', '.doctor', function () {
        $('.doctor, .day').removeClass('is-selected');
        $('.calendar, .schedule').hide();
        loadCalendar($(this).data('id'));
    });

    $(document).on('click', '.slot', function () {

        selectedSlot = $(this).data('id');
        $.post('/block-slot', {doctor: selectedDoctor, day: selectedDay, slot: selectedSlot});

        $('#slotTime').html('Запись на: ' + selectedDay + ' ' + $(this).text());
        $('#selectedDoctor').html('Врач: ' + $('.doctor[data-id="'+selectedDoctor+'"').find('div.doc-name').text());
        $('#selectedService').html('Услуга: ' + $('.service[data-id="'+selectedService+'"').text());

        if (!$(this).hasClass('is-selected')) {
            $('button.confirm-service').show();
        } else {
            $('button.confirm-service').hide();
        }
    });

    $(document).on('click', '.cancel-service, .confirm-service', function () {

        $.post('/unblock-slot', {doctor: selectedDoctor, day: selectedDay, slot: selectedSlot});

        if ($(this).hasClass('confirm-service')) {
            $(this).closest('#timeModal').modal('hide');

            $.post('/create-slot', {doctor: selectedDoctor, day: selectedDay, slot: selectedSlot, service: selectedService}, function () {

                $('#notifyModal').modal('show').find('.modal-body').html('Успешная запись!');
                getSchedule(selectedTimestamp);
                loadMySlots();

            }).fail(function(xhr, status, error) {
                $('#notifyModal').modal('show').find('.modal-body').html(xhr.responseText);
            });
        }

        if ($(this).hasClass('cancel-service') && $('.slot[data-id="'+selectedSlot+'"').hasClass('is-selected')) {

            $.post('/remove-slot', {doctor: selectedDoctor, day: selectedDay, slot: selectedSlot}, function () {

                $('#notifyModal').modal('show').find('.modal-body').html('Успешная отмена записи!');
                getSchedule(selectedTimestamp);
                loadMySlots();

            }).fail(function() {
                $('#notifyModal').modal('show').find('.modal-body').html('Возникла ошибка');
            });
        }
    });

    $('#timeModal').on('hide.bs.modal', function (e) {
        $.post('/unblock-slot', {doctor: selectedDoctor, day: selectedDay, slot: selectedSlot});
    });

    loadServices();
    loadMySlots();

    function updateInfo() {
        if (this.today) {
            // console.log(this.today);
        }

        if (this.lastSelectedDay) {

            getSchedule(this.lastSelectedDay)
        }
    }

    var myCalendar = new HelloWeek({
        selector: '.calendar',
        lang: 'en',
        format: 'DD/MM/YYYY',
        monthShort: true,
        weekShort: true,
        disablePastDays: true,
        multiplePick: false,
        // minDate: 1520696057,
        // maxDate: 1522519829,
        onLoad: updateInfo,
        onChange: updateInfo,
        onSelect: updateInfo
    });

    function getSchedule(day) {

        selectedTimestamp = day;

        $('.schedule').show().html('');

        $.get('/schedule', {day: day, doctorId: selectedDoctor}, function (data) {
            $('.schedule').html('<div>Выберите удобное время:</div>');
            selectedDay = data.day;
            $.each(data.slots, function (k, slot) {
                $('.schedule').append(
                    $("<div></div>")
                        .attr('data-id', k)
                        .addClass('slot')
                        .attr('data-toggle', !slot.busy ? 'modal' : '')
                        .attr('data-target', !slot.busy ? '#timeModal' : '')
                        .addClass(slot.busy ? 'disabled' : '')
                        .addClass(slot.isMy ? 'is-selected' : '')
                        .text(slot.time + (slot.isMy ? ' (ваша запись)' : ''))
                );
            });
        }).fail(function() {
            $('.schedule').html('Ошибка загрузки');
        });
    }

    function loadServices() {
        $.get('/services', {}, function (data) {

            $('.services').html('<div>Выберите услугу:</div>');
            $.each(data, function (k, service) {
                $('.services').append(
                    $("<div></div>")
                        .attr('data-id', service.id)
                        .addClass('service')
                        .text(service.name)
                );
            });
        }).fail(function() {
            $('.services').html('Ошибка загрузки');
        });
    }

    function loadDoctors(serviceId) {
        $.get('/doctors/'+serviceId, {}, function (data) {

            selectedService = serviceId;

            $('.doctors').html('<div>Выберите врача:</div>');
            $.each(data, function (k, doctor) {
                var services = '';
                if (doctor.services.length) {
                    $.each(doctor.services, function (k, service) {
                        services += '<div '+ (service.service_id == selectedService ? 'class="is-selected"':'') +'data-id="'+service.service_id+'">'+service.service.name + ' ' + service.price + 'руб</div>'
                    });
                }
                $('.doctors').append(
                    $("<div></div>")
                        .attr('data-id', doctor.id)
                        .addClass('doctor')
                        .append(
                            $('<div></div>').addClass('doc-name').html(doctor.name)
                        )
                        .append(
                            $('<div></div>').addClass('doctor-services').html(services).hide()
                        )
                );
            });
        }).fail(function() {
            $('.doctors').html('Ошибка загрузки');
        });
    }

    function loadCalendar(doctorId) {
        $('.calendar').show();

        selectedDoctor = doctorId;

        $('.doctor-services').hide();
        $('.doctor[data-id="'+selectedDoctor+'"').find('.doctor-services').show();

        console.log(doctorId);
    }

    function loadMySlots() {
        $.get('/my-slots', {}, function (data) {
            $('.my-slots').html('')
                .append(
                    $('<table></table>')
                        .addClass('table')
                        .append(
                            $('<thead></thead>')
                                .append(
                                    $('<tr></tr>')
                                        .append($('<th></th>').text('День'))
                                        .append($('<th></th>').text('Время'))
                                        .append($('<th></th>').text('Цена'))
                                        .append($('<th></th>').text('Услуга'))
                                        .append($('<th></th>').text('Врач'))
                                )
                        )
                );

            $.each(data, function (k, slot) {
                $('.my-slots').find('table')
                    .append(
                        $('<tbody></tbody>')
                            .append(
                                $('<tr></tr>')
                                    .append($('<td></td>').text(slot.day))
                                    .append($('<td></td>').text(slot.slot))
                                    .append($('<td></td>').text((slot.price / 100) + ' руб'))
                                    .append($('<td></td>').text(slot.service))
                                    .append($('<td></td>').text(slot.doctor))
                            )
                    )
            })
        });
    }
});