<?php

use app\models\AccountantAccountantActivity;
use app\models\AccountantActivity;
use yii\db\Migration;

class m251211_110605_filling_in_accountant_accountant_activity_table extends Migration
{

    private const CSV_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'accountant_activities.json';

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn(AccountantAccountantActivity::tableName(),'created_at', $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn(AccountantAccountantActivity::tableName(),'updated_at', $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'));
        $activityListJson = file_get_contents(self::CSV_FILE);
        $activityList = json_decode($activityListJson, JSON_OBJECT_AS_ARRAY);
        $errors = 0;
        foreach ($activityList as $activity) {
            $accountantName = explode(' ' , $activity['accountant']);
            $accountant = (new \yii\db\Query())
                ->select('id')
                ->from('accountant')
                ->where(['firstname' => $accountantName[0], 'lastname' => $accountantName[1]])
                ->one();
            if ($accountant) {
                $activities = explode(', ', $activity['tasks']);
                foreach ($activities as $activityName) {
                    $activity = AccountantActivity::find()
                        ->where(['name' => $activityName])
                        ->one();
                        if ($activity) {
                            $this->insert(AccountantAccountantActivity::tableName(), [
                                'accountant_id' => $accountant['id'],
                                'accountant_activity_id' => $activity['id'],
                                // 'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        } else {
                            echo "Activity not found: " . $activityName . "\n";
                            $errors++;
                        }
                }
            } else {
                echo "Accountant not found: " . $activity['accountant'] . "\n";
                $errors++;
            }
        }
        if ($errors > 0) {
            echo "Total errors: " . $errors . "\n";
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(AccountantAccountantActivity::tableName(),'created_at');
        $this->dropColumn(AccountantAccountantActivity::tableName(),'updated_at');
        $this->truncateTable(AccountantAccountantActivity::tableName());
    }
}
