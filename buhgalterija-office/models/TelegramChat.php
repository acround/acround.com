<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "telegram_chat".
 *
 * @property int $id
 * @property int $chat_id
 * @property string|null $title
 * @property string|null $type
 *
 * @property TelegramMessage[] $telegramMessages
 * @property TelegramTopic[] $telegramTopics
 */
class TelegramChat extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'telegram_chat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'type'], 'default', 'value' => null],
            [['chat_id'], 'required'],
            [['chat_id'], 'default', 'value' => null],
            [['chat_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 50],
            [['chat_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Chat ID',
            'title' => 'Title',
            'type' => 'Type',
        ];
    }

    /**
     * Gets query for [[TelegramMessages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTelegramMessages()
    {
        return $this->hasMany(TelegramMessage::class, ['chat_id' => 'chat_id']);
    }

    /**
     * Gets query for [[TelegramTopics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTelegramTopics()
    {
        return $this->hasMany(TelegramTopic::class, ['chat_id' => 'chat_id']);
    }

}
