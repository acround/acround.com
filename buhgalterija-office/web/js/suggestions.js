$(document).ready(function () {
    // Делегирование события input для динамически появляющегося #search
    $(document).on('input', '#search', function () {
        var $input = $(this);
        var query = $input.val().trim();
        var $suggestions = $('#suggestions');
        // var $hiddenId = $('#selected_id');
        let typeQuery = $(this).data('type');
        let token = getUser().token;

        // Сброс предыдущего таймера
        if ($input.data('debounceTimer')) {
            clearTimeout($input.data('debounceTimer'));
        }

        // Показываем подсказки только при длине запроса >= 3
        if (query.length < 3) {
            $suggestions.hide();
            return;
        }

        // Устанавливаем новый таймер
        var timer = setTimeout(function () {
            $.ajax({
                url: '/' + typeQuery + '/suggest',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ token: token, query: query, }),
                dataType: 'json',
                success: function (request) {
                    if (request.status == 'success' && Array.isArray(request.data) && request.data.length > 0) {
                        renderSuggestions(request.data, $suggestions);
                    } else {
                        $suggestions.hide();
                    }
                },
                error: function () {
                    $suggestions.hide();
                }
            });
        }, 300);

        $input.data('debounceTimer', timer);
    });

    // Делегирование клика по вариантам (динамически создаваемым внутри #suggestions)
    $(document).on('click', '#suggestions div', function () {
        var $div = $(this);
        var id = $div.data('id');
        var name = $div.text();
        var $input = $('#search');
        var $hiddenId = $('#selected_id');
        var $suggestions = $('#suggestions');

        $input.val(name);
        $hiddenId.val(id);
        $suggestions.hide();

        // Если определена глобальная функция обратного вызова
        if (typeof window.onCompanySelect === 'function') {
            window.onCompanySelect(id, name);
        }
        console.log('Выбрана компания:', { id: id, name: name });
    });

    // ================== Навигация с клавиатуры ==================
    $(document).on('keydown', '#search', function (e) {
        var $input = $(this);
        var $suggestions = $('#suggestions');

        // Если список скрыт или пуст – игнорируем навигацию
        if (!$suggestions.is(':visible') || $suggestions.children().length === 0) {
            return;
        }

        var $items = $suggestions.children('div');
        var currentIndex = $items.index($items.filter('.selected').first()); // -1 если нет выделенного

        // Обработка клавиш
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault(); // убираем прокрутку страницы
                if (currentIndex < $items.length - 1) {
                    setSelected($items.eq(currentIndex + 1));
                } else {
                    // Циклически переходим к первому
                    setSelected($items.first());
                }
                break;

            case 'ArrowUp':
                e.preventDefault();
                if (currentIndex > 0) {
                    setSelected($items.eq(currentIndex - 1));
                } else {
                    // Циклически переходим к последнему
                    setSelected($items.last());
                }
                break;

            case 'Enter':
                e.preventDefault(); // предотвращаем отправку формы, если input внутри form
                if (currentIndex !== -1) {
                    // Имитируем клик по выбранному элементу
                    $items.eq(currentIndex).click();
                }
                break;

            case 'Escape':
                e.preventDefault();
                $suggestions.hide();
                // Сбрасываем выделение (опционально)
                $items.removeClass('selected');
                break;
        }
    });

    // Вспомогательная функция для выделения элемента и прокрутки к нему
    function setSelected($item) {
        var $suggestions = $('#suggestions');
        // Снимаем выделение со всех
        $suggestions.find('.selected').removeClass('selected');
        // Добавляем новому элементу
        $item.addClass('selected');

        // Прокрутка списка, чтобы выделенный элемент был виден
        var container = $suggestions[0];
        var itemTop = $item.position().top; // позиция относительно контейнера
        var itemBottom = itemTop + $item.outerHeight();
        var scrollTop = container.scrollTop;
        var containerHeight = $suggestions.innerHeight();

        if (itemTop < scrollTop) {
            container.scrollTop = itemTop;
        } else if (itemBottom > scrollTop + containerHeight) {
            container.scrollTop = itemBottom - containerHeight;
        }
    }

    // Функция отрисовки вариантов
    function renderSuggestions(items, $suggestions) {
        $suggestions.empty();
        items.forEach(function (item) {
            $('<div>')
                .text(item.name)
                .data('id', item.id)
                .appendTo($suggestions);
        });
        $suggestions.show();
    }

    // Скрытие dropdown при клике вне контейнера
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.suggest-container').length) {
            $('#suggestions').hide();
        }
    });

    // Защита при потере фокуса (чтобы успеть кликнуть на вариант)
    $(document).on('blur', '#search', function () {
        setTimeout(function () {
            if (!$('#suggestions div:hover').length) {
                $('#suggestions').hide();
            }
        }, 200);
    });
});

