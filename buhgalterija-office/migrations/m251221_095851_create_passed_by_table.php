<?php

use app\models\PassedBy;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%passed_by}}`.
 */
class m251221_095851_create_passed_by_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(PassedBy::tableName(), [
            'id' => $this->primaryKey(),
            'tg_id' => $this->bigInteger(),
            'firstname' => $this->string(32),
            'lastname' => $this->string(32),
            'username' => $this->string(32),
            'status' => $this->string(32)->defaultValue('new'),
            'lang' => $this->string(8)->notNull()->defaultValue('ru'),
            'created_at' => $this->timestamp(0)->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp(0)->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Создание индексов
        $this->createIndex(
            '{{%idx-passed_by-tg_id}}',
            PassedBy::tableName(),
            'tg_id',
            true
        );

        $this->createIndex(
            '{{%idx-passed_by-username}}',
            PassedBy::tableName(),
            'username',
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-passed_by-tg_id', PassedBy::tableName());
        $this->dropIndex('idx-passed_by-username', PassedBy::tableName());
        $this->dropTable(PassedBy::tableName());
    }
}
