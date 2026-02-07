<?php

use yii\db\Migration;

class m260201_123142_create_telegram_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // 1. Таблица чатов
        $this->createTable('{{%telegram_chat}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->bigInteger()->notNull()->unique(),
            'title' => $this->string(255),
            'type' => $this->string(50),
        ], $tableOptions);

        // 2. Таблица топиков (форумов в супергруппах)
        $this->createTable('{{%telegram_topic}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->bigInteger()->notNull(),
            'topic_id' => $this->integer()->notNull()->unique(),
            'name' => $this->string(255),
        ], $tableOptions);

        $this->createIndex('idx-telegram_topic-chat_topic', '{{%telegram_topic}}', ['chat_id', 'topic_id'], true);

        // 3. Таблица сообщений
        $this->createTable('{{%telegram_message}}', [
            'id' => $this->primaryKey(),
            'message_id' => $this->bigInteger(),
            'chat_id' => $this->bigInteger(),
            'topic_id' => $this->integer()->defaultValue(null),
            'user_id' => $this->bigInteger(),
            'username' => $this->string(255),
            'text' => $this->text(),
            'created_at' => $this->dateTime(),
            'is_outgoing' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        // Индексы для ускорения выборки сообщений
        $this->createIndex('idx-telegram_message-chat_id', '{{%telegram_message}}', 'chat_id');
        $this->createIndex('idx-telegram_message-user_id', '{{%telegram_message}}', 'user_id');

        // Внешние ключи (опционально, если нужна строгая целостность)
        $this->addForeignKey(
            'fk-telegram_message-chat_id',
            '{{%telegram_message}}',
            'chat_id',
            '{{%telegram_chat}}',
            'chat_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-telegram_message-topic_id',
            '{{%telegram_message}}',
            'topic_id',
            '{{%telegram_topic}}',
            'topic_id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-telegram_topic-chat_id',
            '{{%telegram_topic}}',
            'chat_id',
            '{{%telegram_chat}}',
            'chat_id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-telegram_topic-chat_id', '{{%telegram_topic}}');
        $this->dropForeignKey('fk-telegram_message-topic_id', '{{%telegram_message}}');
        $this->dropForeignKey('fk-telegram_message-chat_id', '{{%telegram_message}}');
        $this->dropTable('{{%telegram_message}}');
        $this->dropTable('{{%telegram_topic}}');
        $this->dropTable('{{%telegram_chat}}');
    }
}
