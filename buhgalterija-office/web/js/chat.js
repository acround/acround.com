/**
 * Менеджер чата
 */
const ChatApp = {
    socket: null,
    chatContainerId: 'messages-container',
    timer: null,

    // Инициализация чата
    init: function () {
        console.log('Запуск инициализации чата...');
        this.loadChatList();
    },

    loadChatList: function () {
        const user = getUser();
        token = user.token;
        let data = { token: token };
        const customerTgId = $('#customer-tg-id').val();
        if (!customerTgId) {
            console.error('Не указан TG ID пользователя');
            return;
        }
        data = { ...data, tg_id: customerTgId };
        $.ajax({
            url: '/chat/chat-list/',
            type: 'POST',
            data: data,
            success: function (response) {
                if (response.status === 'success') {
                    $('#chat-list-container').empty();
                    for (let chat of response.chats) {
                        const chatLink = `<div><button class="load-chat btn btn-link" data-user-id="${chat.user_id}" data-chat-id="${chat.chat_id}" data-topic-id="${chat.topic_id}">${chat.title}</button></div>`;
                        $('#chat-list-container').append(chatLink);
                    }
                } else if (response.status === 'logout') {
                    clearUser();
                    loadContent();
                } else {
                    showError(dictionaryLookup('error', user.lang), response.message);
                }
            },
            error: function (e) {
                showError(dictionaryLookup('error', user.lang), e.message);
            }
        });
        return false;

    },

    // Загрузка истории сообщений из БД через AJAX
    loadHistory: async function (userId, chatId, topicId) {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
        let id = $('#chat-id').val();
        if (id) {
            try {
                const user = getUser();
                const requestdata = {
                    token: user.token,
                    tg_id: userId,
                    chat_id: chatId,
                    topic_id: topicId
                };
                const response = await fetch('/chat/history/', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestdata)
                });
                const data = await response.json();

                const container = document.getElementById(this.chatContainerId);
                if (!container) return;

                // Очищаем контейнер (если там было что-то лишнее)
                container.innerHTML = '';
                $('#chat-last-message-id').val(data.last_message_id);

                // Отрисовываем старые сообщения
                for (let i = 0; i < data.messages.length; i++) {
                    this.renderMessage(data.messages[i], i === data.messages.length - 1); // true только для последнего сообщения
                }

                // Один раз скроллим в самый низ после загрузки всей пачки
                container.scrollTop = container.scrollHeight;

                this.timer = setInterval(this.checkNewMessages, 3000);
            } catch (e) {
                console.error('Ошибка загрузки истории:', e);
            }
        }
    },

    // Удаление чата (вызывается при переключении на другой раздел)
    destroy: function () {
        if (this.socket) {
            this.socket.close(); // Явно закрываем соединение
            this.socket = null;
        }
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
        console.log('Чат деактивирован');
    },

    // Отправка сообщения на бэкенд Yii2 (через AJAX)
    sendMessage: function (text) {
        if (!text.trim()) return;

        const userId = $('#chat-user-id').val();
        const chat_id = $('#chat-id').val();
        const topicId = $('#chat-topic-id').val();

        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // 'X-CSRF-Token': yii.getCsrfToken() // Обязательно для Yii2
            },
            body: JSON.stringify({
                token: getUser().token,
                user_id: userId,
                chat_id: chat_id,
                topic_id: topicId,
                message: text,
            })
        })
            .then(response => response.json())
            .then(data => {
                if (!data.status == 'success') {
                    showError('Sending error');
                // } else {
                //     this.renderMessage(data.message);
                }
            });
    },

    renderMessage: function (data) {
        const container = document.getElementById(this.chatContainerId);
        if (!container) return;

        // Проверяем, находится ли пользователь внизу чата
        const isAtBottom = container.scrollHeight - container.clientHeight <= container.scrollTop + 50;

        // Определяем класс (incoming или outgoing)
        // Допустим, вы передаете в JSON поле is_my: true/false
        const messageClass = data.is_outgoing == 1 ? 'message-outgoing' : 'message-incoming';

        let msgHtml = `<div class="message ${messageClass}" 
                         data-message-id="${data.id}"
                         style="${data.is_outgoing ? 'justify-content: flex-end;' : 'justify-content: flex-start;'}">
                        <div style="max-width: 70%; padding: 8px 12px; border-radius: 8px; background: ${data.is_outgoing ? '#dcf8c6' : '#fff'}; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                            <div style="font-weight: bold; font-size: 0.9em; color: #075e54; margin-bottom: 3px;">
                                ${data.username}
                            </div>
                            <div style="word-wrap: break-word;">
                                ${data.text}
                            </div>
                            <div style="text-align: right; font-size: 0.75em; color: #667781; margin-top: 3px;">
                                ${data.created_at}
                            </div>
                        </div>
                    </div>`;

        container.insertAdjacentHTML('beforeend', msgHtml);

        if (container.children.length > 100) {
            container.removeChild(container.firstChild);
        }

        // Автопрокрутка, если пользователь был внизу
        if (isAtBottom) {
            container.scrollTop = container.scrollHeight;
        }
    },

    checkNewMessages: function () {
        const user = getUser();
        let userId = $('#chat-user-id').val();
        let chatId = $('#chat-id').val();
        let topicId = $('#chat-topic-id').val();
        let last_message_id = $('#chat-last-message-id').val();

        fetch('/chat/check-new-messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ token: user.token, user_id: userId, chat_id: chatId, topic_id: topicId, last_message_id: last_message_id })
        })
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    $('#chat-last-message-id').val(data.last_message_id);
                    data.messages.forEach(msg => {
                        ChatApp.renderMessage(msg);
                    });
                }
            });
    }
};

function handleSendClick() {
    const input = document.getElementById('message-text');
    const text = input.value;
    if (text) {
        ChatApp.sendMessage(text);
        input.value = ''; // Очищаем поле
    }
}

$(document).on('click', '#chat-send-button', (e) => {
    handleSendClick();
});

// При клике на "Открыть чат"
$(document).on('click', '#company-chat', (e) => {
    ChatApp.init();
});

// При клике на "Перейти в другой раздел"
$(document).on('click', 'button[data-chat-close="yes"]', (e) => {
    ChatApp.destroy();
});

$(document).on('click', '.load-chat', (e) => {
    const userId = $(e.target).data('user-id');
    const chatId = $(e.target).data('chat-id');
    const topicId = $(e.target).data('topic-id');
    $('#chat-user-id').val(userId);
    $('#chat-id').val(chatId);
    $('#chat-topic-id').val(topicId);
    $('#chat-header h1').text(e.target.textContent);
    $('.load-chat').removeClass('active');
    $(e.target).addClass('active');
    ChatApp.loadHistory(userId, chatId, topicId);
});
