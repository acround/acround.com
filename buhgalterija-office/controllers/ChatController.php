<?php

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\web\Response;
use app\models\Accountant;
use app\models\TelegramChat;
use app\models\TelegramMessage;
use app\models\TelegramTopic;

class ChatController extends BaseController
{
    public function actionIndex()
    {
        // $this->layout = false;
        $chats = TelegramChat::find()->all();
        return $this->render('index', ['chats' => $chats]);
    }

    public function actionView($chatId, $topicId = null)
    {
        $query = TelegramMessage::find()
            ->where(['chat_id' => $chatId])
            ->orderBy(['created_at' => SORT_ASC]);

        if ($topicId) {
            $query->andWhere(['topic_id' => $topicId]);
        }

        $messages = $query->all();
        $chat = TelegramChat::findOne(['chat_id' => $chatId]);

        return $this->render('view', [
            'messages' => $messages,
            'chat' => $chat,
            'chatId' => $chatId,
            'topicId' => $topicId,
        ]);
    }

    public function actionSend()
    {
        $this->layout = false;
        $request = Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $chatId = $request->post('chat_id');
            $topicId = $request->post('topic_id');
            $message = $request->post('message');

            if ($message) {
                $result = Yii::$app->telegram->sendMessage($chatId, $topicId, $message);
            }
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = [
                'status' => 'success',
                'message' => $result['message'],
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    /**
     * Получение списка чатов
     */
    public function actionChatList()
    {
        $this->layout = false;
        $request = Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $userId = $request->post('tg_id', null);
            if ($userId === null) {
                $response->data = [
                    'user_id' => null,
                    'chats' => [],
                ];
                return;
            }
            $query = (new Query())
                ->select([
                    'tm.user_id',
                    'tm.chat_id',
                    'tm.topic_id',
                    'tc.title AS chat_title',
                    'tt.name AS topic_name',
                    'tc."type" AS topic_type',
                ])
                ->from(['tm' => TelegramMessage::tableName()])
                ->leftJoin(['tc' => TelegramChat::tableName()], 'tc.chat_id = tm.chat_id')
                ->leftJoin(['tt' => TelegramTopic::tableName()], 'tt.chat_id = tm.chat_id AND tm.topic_id = tt.topic_id')
                ->where(['tm.user_id' => $userId])
                ->distinct();
            $chats = $query->all();

            $data = array_map(function ($chat) {
                $title = $chat['topic_name'] ? $chat['chat_title'] . '/' . $chat['topic_name'] : $chat['chat_title'];
                return [
                    'user_id' => $chat['user_id'],
                    'chat_id' => $chat['chat_id'],
                    'topic_id' => $chat['topic_id'] ?? '',
                    'title' => $title,
                    'topic_name' => $chat['topic_name'],
                    'type' => $chat['topic_type'],
                ];
            }, $chats);
            $response->data = [
                'status' => 'success',
                'chats' => $data
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    /**
     * Получение истории сообщений
     */
    public function actionHistory()
    {
        $this->layout = false;
        $limit = 30;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $chatId = $request->post('chat_id', null);
            $topicId = $request->post('topic_id', null);
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;

            $messagesQuery = (new Query())
                ->select(['tm.*'])
                ->from(['tm' => TelegramMessage::tableName()])
                ->andWhere(['tm.chat_id' => $chatId])
                ->orderBy(['tm.created_at' => SORT_DESC])
                ->limit($limit);
            if ($topicId) {
                $messagesQuery->andWhere(['tm.topic_id' => $topicId]);
            } else {
                $messagesQuery->andWhere(['tm.topic_id' => null]);
            }

            $chat = TelegramChat::findOne(['chat_id' => $chatId]);

            if ($topicId) {
                $topic = TelegramTopic::findOne(['chat_id' => $chatId, 'topic_id' => $topicId]);
            } else {
                $topic = null;
            }

            $messages = $messagesQuery->all();

            // Переворачиваем массив, чтобы вверху были старые, а внизу — новые
            $messages = array_reverse($messages);

            $response->data = [
                'status' => 'success',
                'messages' => $messages,
                'chat' => $chat,
                'topic' => $topic,
                'last_message_id' => !empty($messages) ? end($messages)['id'] : 0,
            ];

            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionCheckNewMessages()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $chatId = $request->post('chat_id', null);
            $topicId = $request->post('topic_id', null);
            $lastMessageId = $request->post('last_message_id', 0);
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;

            $messagesQuery = (new Query())
                ->select(['tm.*'])
                ->from(['tm' => TelegramMessage::tableName()])
                ->andWhere(['tm.chat_id' => $chatId])
                ->andWhere(['>', 'tm.id', $lastMessageId])
                ->orderBy(['tm.created_at' => SORT_DESC]);

            if ($topicId) {
                $messagesQuery->andWhere(['tm.topic_id' => $topicId]);
            } else {
                $messagesQuery->andWhere(['tm.topic_id' => null]);
            }

            $messages = $messagesQuery->all();

            $messages = array_reverse($messages);

            $response->data = [
                'status' => 'success',
                'messages' => $messages,
                'last_message_id' => !empty($messages) ? end($messages)['id'] : 0,
            ];

            return $response;
        } else {
            return $this->renderLogout();
        }
    }
}
