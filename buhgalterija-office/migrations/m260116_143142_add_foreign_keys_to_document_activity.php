<?php

use app\models\Accountant;
use app\models\Document;
use app\models\DocumentActivity;
use app\models\DocumentStep;
use yii\db\Migration;

class m260116_143142_add_foreign_keys_to_document_activity extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Foreign Key для document_id
        $this->addForeignKey(
            'fk_document_activity__document',
            DocumentActivity::tableName(),
            'document_id',
            Document::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        // 2. Foreign Key для accountant_id
        $this->addForeignKey(
            'fk_document_activity_document',
            DocumentActivity::tableName(),
            'accountant_id',
            Accountant::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );

        // 3. Foreign Key для step_id
        $this->addForeignKey(
            'fk_document_activity__document_step',
            DocumentActivity::tableName(),
            'step_id',
            DocumentStep::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаляем ключи в обратном порядке
        $this->dropForeignKey('fk_document_activity__document_step', DocumentActivity::tableName());
        $this->dropForeignKey('fk_document_activity_document', DocumentActivity::tableName());
        $this->dropForeignKey('fk_document_activity__document', DocumentActivity::tableName());
    }
}
