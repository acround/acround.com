<?php

use app\models\Company;
use yii\db\Migration;

class m260210_085127_company_drop_not_used_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Company::tableName();
        $columns = [
            'specific_reports',
            'reminder',
            'report_date',
            'embedding',
        ];
        foreach ($columns as $column) {
            if (Yii::$app->db->getTableSchema($tableName)->getColumn($column)) {
                $this->dropColumn($tableName, $column);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $tableName = Company::tableName();
        $this->addColumn($tableName, 'specific_reports', $this->string(256)->null());
        $this->addColumn($tableName, 'reminder', $this->string(256)->null());
        $this->addColumn($tableName, 'report_date', $this->timestamp()->null());
        $this->addColumn($tableName, 'embedding', 'public.vector NULL');
    }
}
