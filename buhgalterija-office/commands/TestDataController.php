<?php

namespace app\commands;

use app\services\AuthService;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\VarDumper;

class TestDataController extends Controller
{

    const TEST_DIRECTORY = '@app/test/';
    const TEST_DATA_COMPANY_FILE_NAME = 'test_data_company.json';
    const TEST_DATA_CUSTOMER_FILE_NAME = 'test_data_customer.json';
    const TEST_DATA_ACCOUNTANT_FILE_NAME = 'test_data_accountant.json';
    const TEST_DATA_TASK_FILE_NAME = 'test_data_task.json';

    public function actionIndex()
    {
        echo "TestDataController command\n\n";
        try {
            $db = Yii::$app->getDb();
            $db->transaction(function () {
                // $this->actionClear();
                $this->load();
            });
            echo "Test data loaded successfully\n\n";
            return ExitCode::OK;
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage() . "\n\n";
            echo 'File: ' . $e->getFile() . "\n\n";
            echo 'Line: ' . $e->getLine() . "\n\n";
            echo VarDumper::dumpAsString($e->getTraceAsString());
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }

    public function actionClear()
    {
        try {
            $db = Yii::$app->getDb();
            $db->transaction(function () {
                $this->clear();
            });
            echo "Test data cleaned successfully\n\n";
            return ExitCode::OK;
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage() . "\n\n";
            echo 'File: ' . $e->getFile() . "\n\n";
            echo 'Line: ' . $e->getLine() . "\n\n";
            echo VarDumper::dumpAsString($e->getTraceAsString());
            return ExitCode::UNSPECIFIED_ERROR;
        }
        return ExitCode::OK;
    }

    private function clear()
    {
        $fileAccountant = file_get_contents(realpath(Yii::getAlias(self::TEST_DIRECTORY)) . '/' . self::TEST_DATA_ACCOUNTANT_FILE_NAME);
        $accountants = json_decode($fileAccountant, true);
        $emails = array_map(fn($user) => $user['email'], $accountants);
        $deleteQuery = 'DELETE FROM "accountant" WHERE email = :email';
        foreach ($emails as $email) {
            Yii::$app->db->createCommand($deleteQuery, [':email' => $email])->execute();
        }
        echo "Test accountants cleared\n\n";
        $fileTask = file_get_contents(realpath(Yii::getAlias(self::TEST_DIRECTORY)) . '/' . self::TEST_DATA_TASK_FILE_NAME);
        $tasks = json_decode($fileTask, true);
        $requests = array_map(fn($task) => $task['request'], $tasks);
        $deleteQuery = 'DELETE FROM "task" WHERE request = :request';
        foreach ($requests as $request) {
            Yii::$app->db->createCommand($deleteQuery, [':request' => $request])->execute();
        }
        echo "Test tasks cleared\n\n";
        $fileCustomers = file_get_contents(realpath(Yii::getAlias(self::TEST_DIRECTORY)) . '/' . self::TEST_DATA_CUSTOMER_FILE_NAME);
        $customers = json_decode($fileCustomers, true);
        $tgIds = array_map(fn($user) => $user['tg_id'], $customers);
        $deleteQuery = 'DELETE FROM "customer" WHERE tg_id = :tg_id';
        foreach ($tgIds as $tgId) {
            Yii::$app->db->createCommand($deleteQuery, [':tg_id' => $tgId])->execute();
        }
        echo "Test customers cleared\n\n";
        $fileCompany = file_get_contents(realpath(Yii::getAlias(self::TEST_DIRECTORY)) . '/' . self::TEST_DATA_COMPANY_FILE_NAME);
        $companies = json_decode($fileCompany, true);
        $companyNames = array_map(fn($company) => $company['name'], $companies);
        $deleteQuery = 'DELETE FROM "company" WHERE name = :companyName';
        foreach ($companyNames as $companyName) {
            Yii::$app->db->createCommand($deleteQuery, [':companyName' => $companyName])->execute();
        }
        echo "Test companies cleared\n\n";
    }

    private function load()
    {
        $fileCompany = file_get_contents(realpath(Yii::getAlias(self::TEST_DIRECTORY)) . '/' . self::TEST_DATA_COMPANY_FILE_NAME);
        $companies = json_decode($fileCompany, true);
        $nextCompanyIdQuery = 'SELECT nextval(\'company_id_seq\'::regclass)';
        $insertCompanyQuery = 'INSERT INTO "company" ("id","name","name_tg","type_id","is_pdv","activity_id","specific_reports","reminder","pib","status","report_date") VALUES (:id,:name,:name_tg,:type_id,:is_pdv,:activity_id,:specific_reports,:reminder,:pib,:status,:report_date) RETURNING id';
        $nextCustomerIdQuery = 'SELECT nextval(\'customer_id_seq\'::regclass)';
        $insertCustomerQuery = 'INSERT INTO "customer" ("id","tg_id","firstname","lastname","username","status","lang") VALUES (:id,:tg_id,:firstname,:lastname,:username,:status,:lang) RETURNING id';
        $insertCompanyCustomerQuery = 'INSERT INTO "company_customer" ("company_id","customer_id") VALUES (:company_id,:customer_id) RETURNING id';
        $nextAccountantIdQuery = 'SELECT nextval(\'accountant_id_seq\'::regclass)';
        $insertAccountantQuery = 'INSERT INTO "accountant" (id, firstname, lastname, "rule", lang, email, "password", token) VALUES (:id, :firstname, :lastname, :rule, :lang, :email, :password, :token) RETURNING id';
        $nextTaskIdQuery = 'SELECT nextval(\'task_id_seq\'::regclass)';
        $insertTaskQuery = 'INSERT INTO "task" ("id","company_id","category","request","status","due_date","accountant_id","priority") VALUES (:id,:company_id,:category,:request,:status,:due_date,:accountant_id,:priority) RETURNING id';
        $nextCompanyId = 0;
        $accountantId = 0;
        $lastCompany = 0;
        foreach ($companies as $company) {
            $nextCompanyId = Yii::$app->db
                ->createCommand($nextCompanyIdQuery)
                ->queryScalar();
            $company['id'] = $nextCompanyId;
            $lastCompany = $nextCompanyId;
            Yii::$app->db->createCommand($insertCompanyQuery, $company)->execute();
        }
        $fileCustomers = file_get_contents(realpath(Yii::getAlias(self::TEST_DIRECTORY)) . '/' . self::TEST_DATA_CUSTOMER_FILE_NAME);
        $customers = json_decode($fileCustomers, true);
        foreach ($customers as $customer) {
            // $customer['company_id'] = $nextCompanyId;
            $customer['id'] = Yii::$app->db
                ->createCommand($nextCustomerIdQuery)
                ->queryScalar();
            Yii::$app->db->createCommand($insertCustomerQuery, $customer)->execute();
            $companyCustomer = [
                'company_id' => $lastCompany,
                'customer_id' => $customer['id'],
            ];
            Yii::$app->db->createCommand($insertCompanyCustomerQuery, $companyCustomer)->execute();
        }
        $fileAccountant = file_get_contents(realpath(Yii::getAlias(self::TEST_DIRECTORY)) . '/' . self::TEST_DATA_ACCOUNTANT_FILE_NAME);
        $accountants = json_decode($fileAccountant, true);
        foreach ($accountants as $accountant) {
            $accountant['password'] = AuthService::encodePassword($accountant['password']);
            $accountant['id'] = Yii::$app->db
                ->createCommand($nextAccountantIdQuery)
                ->queryScalar();
            $id = Yii::$app->db->createCommand($insertAccountantQuery, $accountant)->execute();
            if ($accountant['rule'] === 'accountant') {
                $accountantId = $id;
            }
        }
        $fileTask = file_get_contents(realpath(Yii::getAlias(self::TEST_DIRECTORY)) . '/' . self::TEST_DATA_TASK_FILE_NAME);
        $tasks = json_decode($fileTask, true);
        foreach ($tasks as $task) {
            $task['company_id'] = $nextCompanyId;
            $task['accountant_id'] = $accountantId;
            $task['id'] = Yii::$app->db
                ->createCommand($nextTaskIdQuery)
                ->queryScalar();
            Yii::$app->db->createCommand($insertTaskQuery, $task)->execute();
        }
    }
}
