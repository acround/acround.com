<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\services\AuthService;

class UserTestController extends Controller
{
    private $testUsers = [
        [
            'id' => 0,
            'firstname' => 'user',
            'lastname' => 'Accountant',
            'rule' => 'accountant',
            'lang' => 'ru',
            'email' => 'accountant@gmail.rs',
            'password' => '12cool09',
            'token' => '',
        ],
        [
            'id' => 0,
            'firstname' => 'user',
            'lastname' => 'Admin',
            'rule' => 'admin',
            'lang' => 'ru',
            'email' => 'admin@gmail.rs',
            'password' => '12cool09',
            'token' => '',
        ],
        [
            'id' => 0,
            'firstname' => 'user',
            'lastname' => 'Ceo',
            'rule' => 'ceo',
            'lang' => 'ru',
            'email' => 'ceo@gmail.rs',
            'password' => '12cool09',
            'token' => '',
        ],
    ];

    public function actionIndex()
    {
        $nextIdQuery = 'SELECT nextval(\'accountant_id_seq\'::regclass)';
        $insertQuery = 'INSERT INTO "accountant" (id, firstname, lastname, "rule", lang, email, "password", token) VALUES (:id, :firstname, :lastname, :rule, :lang, :email, :password, :token)';
        foreach ($this->testUsers as $accountant) {
            $nextId = Yii::$app->db
                ->createCommand($nextIdQuery)
                ->queryScalar();
            $accountant['id'] = $nextId;
            $accountant['password'] = AuthService::encodePassword($accountant['password']);
            Yii::$app->db->createCommand($insertQuery, $accountant)->execute();
        }
        echo count($this->testUsers).' have been inserted'."\n\n";
        return ExitCode::OK;
    }

    public function actionClear()
    {
        $emails = array_map(fn($user) => $user['email'], $this->testUsers);
        $deleteQuery = 'DELETE FROM "accountant" WHERE email IN (:emails)';
        Yii::$app->db->createCommand($deleteQuery, [':emails' => $emails])->execute();
        echo "Test users cleared\n\n";
        return ExitCode::OK;
    }
}
