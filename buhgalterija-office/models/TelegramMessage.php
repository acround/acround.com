<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "telegram_message".
 *
 * @property int $id
 * @property int|null $message_id
 * @property int|null $chat_id
 * @property int|null $topic_id
 * @property int|null $user_id
 * @property string|null $username
 * @property string|null $text
 * @property string|null $created_at
 * @property int|null $is_outgoing
 *
 * @property TelegramChat $chat
 * @property TelegramTopic $topic
 */
class TelegramMessage extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'telegram_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message_id', 'chat_id', 'topic_id', 'user_id', 'username', 'text', 'created_at'], 'default', 'value' => null],
            [['is_outgoing'], 'default', 'value' => 0],
            [['message_id', 'chat_id', 'topic_id', 'user_id', 'is_outgoing'], 'default', 'value' => null],
            [['message_id', 'chat_id', 'topic_id', 'user_id', 'is_outgoing'], 'integer'],
            [['text'], 'string'],
            [['created_at'], 'safe'],
            [['username'], 'string', 'max' => 255],
            [['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => TelegramChat::class, 'targetAttribute' => ['chat_id' => 'chat_id']],
            [['topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => TelegramTopic::class, 'targetAttribute' => ['topic_id' => 'topic_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message_id' => 'Message ID',
            'chat_id' => 'Chat ID',
            'topic_id' => 'Topic ID',
            'user_id' => 'User ID',
            'username' => 'Username',
            'text' => 'Text',
            'created_at' => 'Created At',
            'is_outgoing' => 'Is Outgoing',
        ];
    }

    /**
     * Gets query for [[Chat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChat()
    {
        return $this->hasOne(TelegramChat::class, ['chat_id' => 'chat_id']);
    }

    /**
     * Gets query for [[Topic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTopic()
    {
        return $this->hasOne(TelegramTopic::class, ['topic_id' => 'topic_id']);
    }

}
