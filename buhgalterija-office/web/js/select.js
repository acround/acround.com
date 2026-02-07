
function getSelectWidgetValue(id) {
    let widget = $('#' + id);
    if (widget.length === 0) {
        return null;
    } else {
        return widget.find('input.select-widget-value').val();
    }
}

// 1. Открытие/закрытие по клику на триггер
$(document).on('click', '.select-widget-trigger', function (e) {
    $(this).parent('.select-widget').toggleClass('open');
});

// 2. Выбор опции
$(document).on('click', '.select-widget-option', function (e) {
    let value = $(this).data('value');
    let text = $(this).text();
    let wrapper = $(this).closest('.select-widget-wrapper');

    // Обновляем текст в триггере и скрытый инпут
    $(wrapper).find('div.select-widget-trigger').text(text);
    $(wrapper).find('input.select-widget-value').val(value);

    // Закрываем список
    $(wrapper).find('.select-widget').removeClass('open');
});

// 3. Закрытие списка, если кликнули вне его области
$(window).on('click', function (e) {
    if (!$(e.target).closest('.select-widget').length) {
        $('.select-widget').removeClass('open');
    }
});
