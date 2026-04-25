<?php

use app\models\Company;
use yii\db\Migration;

class m260210_110940_companies_without_pib_are_inactive extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = "UPDATE company SET status = '" . Company::COMPANY_STATUS_INACTIVE . "' WHERE pib IS NULL OR pib = 0";
        $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {}
}
