<?php

use app\models\Accountant;
use app\models\Company;
use app\models\CompanyNotes;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%company_notes}}`.
 */
class m251209_110549_create_company_notes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(CompanyNotes::tableName(), [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull(),
            'note' => $this->string(200),
            'accountant_id' => $this->integer()->notNull(),
            'status' => $this->string(16)->notNull()->defaultValue('active'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex(
            '{{%idx-company_notes-company_id}}',
            CompanyNotes::tableName(),
            'company_id'
        );
        $this->addForeignKey(
            '{{%fk-company_notes-company_id}}',
            CompanyNotes::tableName(),
            'company_id',
            Company::tableName(),
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%fk-company_notes-accountant_id}}',
            CompanyNotes::tableName(),
            'accountant_id',
            Accountant::tableName(),
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-company_notes-company_id}}',
            CompanyNotes::tableName()
        );
        $this->dropForeignKey(
            '{{%fk-company_notes-accountant_id}}',
            CompanyNotes::tableName()
        );
        $this->dropIndex(
            '{{%idx-company_notes-company_id}}',
            CompanyNotes::tableName()
        );
        $this->dropTable(CompanyNotes::tableName());
    }
}
