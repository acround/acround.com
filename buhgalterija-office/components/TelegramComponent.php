<?php

namespace app\components;

use yii\base\Component;
use TelegramBot\Api\BotApi;
use app\models\TelegramMessage;
use app\models\TelegramChat;
use app\models\TelegramTopic;

class TelegramComponent extends Component
{
    public $botToken;
    private $bot;

    public function init()
    {
        parent::init();
        $this->bot = new BotApi($this->botToken);
    }

    public function processWebhook($message)
    {
        if (!$message) {
            return;
        }

        $chatId = $message['chat_id'] ?? null;
        $topicId = $message['topic'] ? $message['topic']['message_thread_id'] : null;
        $userId = $message['tg_id'] ?? null;
        $userName = $message['user']['username'] ?? 'unknown';

        // Сохраняем чат
        $chat = TelegramChat::findOne(['chat_id' => $chatId]);
        if (!$chat) {
            $chat = new TelegramChat();
            $chat->chat_id = $chatId;
            $chat->title = $message['chat_title'] ?? '@' . $userName;
            $chat->type = $message['chat_type'] ?? 'private';
            $chat->save();
        }

        // Сохраняем тему (если есть)
        if ($topicId) {
            $topicName = $message['topic']['name'] ?? 'Topic ' . $topicId;
            $topic = TelegramTopic::findOne(['chat_id' => $chatId, 'topic_id' => $topicId]);
            if (!$topic) {
                $topic = new TelegramTopic();
                $topic->chat_id = $chatId;
                $topic->topic_id = $topicId;
                $topic->name = $topicName;
                $topic->save();
            }
        }

        // Сохраняем сообщение
        $msg = new TelegramMessage();
        $msg->message_id = $message['message_id'] ?? null;
        $msg->chat_id = $chatId;
        $msg->topic_id = $topicId;
        $msg->user_id = $userId;
        $msg->username = $userName;
        $msg->text = $message['text'] ?? null;
        $msg->created_at = date('Y-m-d H:i:s');//, $message->getDate());
        $msg->is_outgoing = 0;
        $msg->save();
    }

    public function sendMessage($chatId, $topicId = null, $text = '')
    {
        $params = ['chat_id' => $chatId, 'text' => $text];

        if ($topicId) {
            $params['message_thread_id'] = $topicId;
        }

        $result = $this->bot->sendMessage(
            $params['chat_id'],
            $params['text'],
            null,
            false,
            null,
            null,
            false,
            $topicId
        );

        // Сохраняем отправленное сообщение
        $msg = new TelegramMessage();
        $msg->message_id = $result->getMessageId();
        $msg->chat_id = $chatId;
        $msg->topic_id = $topicId;
        $msg->user_id = $result->getFrom()->getId();
        $msg->username = 'Bot';
        $msg->text = $text;
        $msg->created_at = date('Y-m-d H:i:s');
        $msg->is_outgoing = 1;
        $msg->save();

        return [
            'send' => $result,
            'message' => $msg
        ];
    }
}
