<?php

use yii\db\Migration;
use app\models\Company;
use yii\db\Query;

class m251208_123539_insert_companies extends Migration
{
    private const CSV_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'companies.json';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn(Company::tableName(), 'status', $this->string(32)->defaultValue('onboarding'));
        $companyListJson = file_get_contents(self::CSV_FILE);
        $companyList = json_decode($companyListJson, JSON_OBJECT_AS_ARRAY);
        $nextIdQuery = 'SELECT nextval(\'company_id_seq\'::regclass)';
        $getTypeIdQuery = (new Query())->select('id')->from('company_type');
        $getActivityIdQuery = (new Query())->select('id')->from('company_activities');
        $num = 0;
        foreach ($companyList as $companyRaw) {
            $company = new Company();
            $company->id = Yii::$app->db
                ->createCommand($nextIdQuery)
                ->queryScalar();
            $company->name = $companyRaw['name'];
            $company->name_tg = $companyRaw['name_tg'];
            $type = $companyRaw['type'];
            $company->type_id = $getTypeIdQuery->where('name = :name', ['name' => $type])->one()['id'];
            $company->is_pdv = $companyRaw['is_pdv'] == 'true' ? true : ($companyRaw['is_pdv'] == 'false' ? false : null);
            $company->activity_id = $getActivityIdQuery->where('name = :name', ['name' => $companyRaw['activity']])->one()['id'] ?? null;
            $company->specific_reports = $companyRaw['specific_reports'];
            $company->reminder = $companyRaw['reminder'];
            $company->pib = null;
            if ($company->insert()) {
                $num++;
            } else {
                echo $company->name." insert error\n";
                print_r($company->getErrors());
                // var_dump($company->type_id);
                // var_dump($company->activity_id);
                echo "\n\n";
            }
        }
        echo $num . " companies have been inserted\n\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('TRUNCATE TABLE company RESTART IDENTITY CASCADE');
        $this->execute("ALTER SEQUENCE company_id_seq RESTART WITH 1");
    }
}
