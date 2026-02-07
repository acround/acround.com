<?php

use app\models\Document;
use app\models\DocumentStep;
use yii\db\Migration;

class m251225_151938_fix_document_steps extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach (Document::$statuses as $status) {
            $step = DocumentStep::findOne(['name' => $status]);
            if (!$step) {
                $step = $this->insert(DocumentStep::tableName(), [
                    'name' => $status,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251225_151938_fix_document_steps cannot be reverted.\n";

        return false;
    }
}
