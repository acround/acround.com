<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $chat ? $chat->title : 'Чат';
$this->registerJsFile('@web/js/chat.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('@web/css/chat.css');
?>

<div id="chat-view" class="chat-view">
    <div id="chat-header" class="chat-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="chat-container">
        <div id="messages-container" class="messages-container">
            <div class="text-center text-muted" style="padding: 50px 0;">
                Нет сообщений
            </div>
        </div>

        <div class="message-input-container">
            <form id="message-form">
                <?= Html::hiddenInput('user_id', '', ['id' => 'chat-user-id']) ?>
                <?= Html::hiddenInput('chat_id', $chat->chat_id, ['id' => 'chat-id']) ?>
                <?= Html::hiddenInput('topic_id', $topic ? $topic->topic_id : null, ['id' => 'chat-topic-id']) ?>
                <?= Html::hiddenInput('last_message_id', 0, ['id' => 'chat-last-message-id']) ?>

                <div style="display: flex; gap: 10px;">
                    <?= Html::textarea('text', '', [
                        'id' => 'message-text',
                        'rows' => 2,
                        'placeholder' => 'Введите сообщение... (Ctrl+Enter для отправки)',
                    ]) ?>
                    <?= Html::button('Отправить', [
                        'class' => 'send-button btn btn-primary',
                        'id' => 'chat-send-button',
                    ]) ?>
                </div>

                <div id="status-message"></div>
            </form>
        </div>
    </div>
</div>