// ================================================================
// КЛАСС МОДАЛЬНОГО ОКНА
// ================================================================
class Modal {
    constructor(modalId = 'modal-overlay', headerText = '', type = 'calendar') {
        let user = getUser();
        this.modal = document.getElementById(modalId);
        this.currentRow = null; // Здесь будем хранить нажатую строку <tr>
        this.doButtonUrl = null;

        $(this.modal).find('.modal-header h3').text(headerText);
        if (type === 'calendar') {
            $(this.modal).find('.modal-body').empty().append('<table><thead><tr><th>' + dictionaryLookup('company', user.lang) + '</th><th>' + dictionaryLookup('reminder', user.lang) + '</th></tr></thead><tbody></tbody></table>');
        }

        this.close = this.close.bind(this);
        this.onOverlayClick = this.onOverlayClick.bind(this);
        this.onEscPress = this.onEscPress.bind(this);
        this._initEvents();
    }
    // Приватный метод для назначения обработчиков (эмулируем приватность)
    _initEvents() {
        // 1. Находим все элементы внутри модалки, которые должны её закрывать
        // (крестик и кнопка "Отмена")
        const closeButtons = this.modal.querySelectorAll('.modal-close-btn, .btn-secondary');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', this.close);
        });

        // 2. Закрытие по клику на оверлей
        this.modal.addEventListener('click', this.onOverlayClick);
    }

    setDoUrl(url) {
        this.doButtonUrl = url;
        $(this.modal).find('#do-action-url').val(url);
    }

    setContent(htmlContent) {
        const body = this.modal.querySelector('.modal-body');
        body.innerHTML = htmlContent;
    }

    open(clickedElement) {
        this.currentRow = clickedElement; // Сохраняем строку в память класса
        this.modal.classList.add('open');
        document.body.classList.add('no-scroll');
        document.addEventListener('keydown', this.onEscPress);
    }

    close() {
        this.modal.classList.remove('open');
        document.body.classList.remove('no-scroll');
        document.removeEventListener('keydown', this.onEscPress);
    }

    // Обработчик клика по фону
    onOverlayClick(event) {
        if (event.target === this.modal) {
            this.close();
        }
    }

    // Обработчик клавиши ESC
    onEscPress(event) {
        if (event.key === 'Escape') {
            this.close();
        }
    }
}
