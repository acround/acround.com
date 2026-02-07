<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "telegram_topic".
 *
 * @property int $id
 * @property int $chat_id
 * @property int $topic_id
 * @property string|null $name
 *
 * @property TelegramChat $chat
 * @property TelegramMessage[] $telegramMessages
 */
class TelegramTopic extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'telegram_topic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'default', 'value' => null],
            [['chat_id', 'topic_id'], 'required'],
            [['chat_id', 'topic_id'], 'default', 'value' => null],
            [['chat_id', 'topic_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['chat_id', 'topic_id'], 'unique', 'targetAttribute' => ['chat_id', 'topic_id']],
            [['topic_id'], 'unique'],
            [['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => TelegramChat::class, 'targetAttribute' => ['chat_id' => 'chat_id']],
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
            'topic_id' => 'Topic ID',
            'name' => 'Name',
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
     * Gets query for [[TelegramMessages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTelegramMessages()
    {
        return $this->hasMany(TelegramMessage::class, ['topic_id' => 'topic_id']);
    }

}
