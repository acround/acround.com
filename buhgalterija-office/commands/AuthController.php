<?php

namespace app\commands;

use app\services\AuthService;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\rbac\DbManager;

class AuthController extends Controller
{
    private $rules = [
        'accountant',
        'admin',
        'ceo'
    ];
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        echo get_class($auth) . "\n\n";
        foreach ($this->rules as $rule) {
            $permissions = $auth->getPermissionsByRole($rule);
            echo $rule . ":\n\n";
            $names = array_keys($permissions);
            sort($names);
            foreach ($names as $name) {
                echo $name . "\n";
            }
            echo "\n\n";
        }

        return ExitCode::OK;
    }

    public function actionPwd()
    {
        $file = file_get_contents(Yii::getAlias('@app/test/acc-pwd.json'));
        $users = json_decode($file, true);

        foreach ($users as $user) {
            $password = AuthService::encodePassword($user['password']);
            Yii::$app->db->createCommand()
                ->update('{{%accountant}}', ['password' => $password], ['email' => $user['email']])
                ->execute();
            echo "Updated password for " . $user['email'] . "\n";
        }
        return ExitCode::OK;
    }
}
