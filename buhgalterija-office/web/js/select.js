
function isVisible(container, el) {
    let $el = $(el);
    let $container = $(container);

    let elTop = $el.position().top; // Позиция относительно верха контейнера
    let elBottom = elTop + $el.outerHeight();
    let containerHeight = $container.innerHeight();

    // Элемент виден, если он не выше верхнего края и не ниже нижнего
    return elTop >= 0 && elBottom <= containerHeight;
}

function scrollToElement($container, $el) {
    if (!isVisible($el, $container)) {
        $el.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
        // let containerScrollTop = $container.scrollTop();
        // let elTop = $el.position().top;

        // $container.stop().animate({
        //     scrollTop: containerScrollTop + elTop
        // }, 200);
    }
}

function getSelectWidgetValue(id) {
    let widget = $('#' + id);
    if (widget.length === 0) {
        return null;
    } else {
        return widget.find('input.select-widget-value').val();
    }
}

$(document).on('click', '.select-widget-trigger', function (e) {
    $(this).parent('.select-widget').toggleClass('open');
});

$(document).on('click', '.select-widget-option', function (e) {
    let value = $(this).data('value');
    let text = $(this).text();
    let wrapper = $(this).closest('.select-widget-wrapper');

    // Обновляем текст в триггере и скрытый инпут
    $(wrapper).find('div.select-widget-trigger').text(text);
    $(wrapper).find('input.select-widget-value').val(value);
    $(wrapper).find('input.select-widget-input').val('');
    // $(wrapper).find('input.select-widget-backup').val(text);
    $(wrapper).find('div.select-widget-options span.select-widget-option.selected').removeClass('selected');
    $(this).addClass('selected');
    $(wrapper).find('.select-widget-option').each(function () {
        $(this).show();
    });

    // Закрываем список
    $(wrapper).find('.select-widget').removeClass('open');
});

// 3. Закрытие списка, если кликнули вне его области
$(window).on('click', function (e) {
    if (!$(e.target).closest('.select-widget').length) {
        $('.select-widget').removeClass('open');
    }
});

$(document).on('keydown', 'div.select-widget-wrapper', function (e) {
    // console.dir(e.originalEvent.key)
    let selected;
    let widget = $(this).find('div.select-widget');
    let trigger = $(this).find('div.select-widget-trigger');
    let input = $(this).find('input.select-widget-input');
    let value = $(this).find('input.select-widget-value');
    let backup = $(this).find('input.select-widget-backup');
    let inputText;
    let backupText;
    switch (e.originalEvent.key) {
        case 'Enter':
            selected = $(this).find('div.select-widget-options span.select-widget-option.selected');
            if (selected.length != 0) {
                value.val($(selected).data('value'));
                input.val('');
                trigger.text($(selected).text());
                // backup.val($(selected).text());
                $(widget).find('.select-widget-option').each(function () {
                    $(this).show();
                });
            }
            widget.toggleClass('open');
            break;
        case 'ArrowDown':
            if (!widget.hasClass('open')) {
                widget.addClass('open');
            } else {
                selected = $(this).find('div.select-widget-options span.select-widget-option.selected');
                if (selected.length == 0) {
                    selected = $(this).find('div.select-widget-options span.select-widget-option:visible').first();
                } else {
                    selected = selected.nextAll('.select-widget-option:visible').first();
                }
                if (selected.length > 0) {
                    $(this).find('div.select-widget-options span.select-widget-option.selected').removeClass('selected');
                    selected.addClass('selected');
                    scrollToElement(widget, selected.get(0));
                }
            }
            break;
        case 'ArrowUp':
            if (widget.hasClass('open')) {
                selected = $(this).find('div.select-widget-options span.select-widget-option.selected');
                if (selected.length == 0) {
                    selected = $(this).find('div.select-widget-options span.select-widget-option:visible').last();
                } else {
                    selected = selected.prevAll('.select-widget-option:visible').first();
                }
                if (selected.length) {
                    $(this).find('div.select-widget-options span.select-widget-option.selected').removeClass('selected');
                    selected.addClass('selected');
                    scrollToElement(widget, selected.get(0));
                }
            }
            break;
        case 'Escape':
            widget.removeClass('open');
            trigger.text(backup.val());
            input.val('');
            value.val(0);
            $(widget).find('.select-widget-option').each(function () {
                $(this).show();
            });
            break;
        case 'Tab':
            widget.removeClass('open');
            break;
        case 'Shift':
        case 'Control':
        case 'Alt':
        case 'Meta':
            break;
        case 'CapsLock':
        case 'NumLock':
        case 'ScrollLock':
        case 'Insert':
        case 'Home':
        case 'PageUp':
        case 'Delete':
        case 'End':
        case 'PageDown':
            break;
        case 'Backspace':
            inputText = input.val();
            if (inputText.length > 0) {
                inputText = inputText.substring(0, inputText.length - 1);
            }
            $(input).val(inputText);
            if (inputText.length > 0) {
                trigger.text(inputText);
                $(widget).find('.select-widget-option').each(function () {
                    if ($(this).text().toLowerCase().includes(inputText.toLowerCase())) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                backupText = backup.val();
                trigger.text(backupText);
                value.val(0);
                input.val('');
                $(widget).find('.select-widget-option').each(function () {
                    $(this).show();
                });
            }
            break;
        default:
            if (e.originalEvent.key.length == 1) {
                if (!widget.hasClass('open')) {
                    widget.addClass('open');
                }
                inputText = input.val() + e.originalEvent.key;
                $(input).val(inputText);
                trigger.text(inputText);
                $(widget).find('.select-widget-option').each(function () {
                    if ($(this).text().toLowerCase().includes(inputText.toLowerCase())) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
    }

});
