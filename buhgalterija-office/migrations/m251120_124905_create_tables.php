<?php

use app\models\Accountant;
use app\models\AccountantAccountantActivity;
use app\models\AccountantActivity;
use app\models\Company;
use app\models\CompanyAccountant;
use app\models\CompanyActivities;
use app\models\CompanyType;
use app\models\Customer;
use app\models\Event;
use app\models\Reminder;
use app\models\ReminderTemplate;
use app\models\Task;
use yii\db\Migration;

class m251120_124905_create_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(CompanyType::tableName(), [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'name' => $this->string(512),
        ]);
        $this->createIndex(
            'idx_company_type_name',
            CompanyType::tableName(),
            'name',
            true
        );
        $this->insert(CompanyType::tableName(), ['name' => 'DOO']);
        $this->insert(CompanyType::tableName(), ['name' => 'Knigaš']);
        $this->insert(CompanyType::tableName(), ['name' => 'Paušal']);

        $this->createTable(CompanyActivities::tableName(), [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'name' => $this->string(32),
        ]);
        $this->createIndex(
            'idx_company_activities_name',
            CompanyActivities::tableName(),
            'name',
            true
        );
        $this->insert(CompanyActivities::tableName(), ['name' => 'trgovina']);
        $this->insert(CompanyActivities::tableName(), ['name' => 'programiranje']);
        $this->insert(CompanyActivities::tableName(), ['name' => 'reklama']);
        $this->insert(CompanyActivities::tableName(), ['name' => 'kafić']);
        $this->insert(CompanyActivities::tableName(), ['name' => 'sushi']);
        $this->insert(CompanyActivities::tableName(), ['name' => 'konsalting']);
        $this->insert(CompanyActivities::tableName(), ['name' => 'prozori']);
        $this->insert(CompanyActivities::tableName(), ['name' => 'posrednik']);
        $this->insert(CompanyActivities::tableName(), ['name' => 'obrazovanje']);
        $this->insert(CompanyActivities::tableName(), ['name' => 'turizam']);

        $this->createTable(Company::tableName(), [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'name' => $this->string(512),
            'name_tg' => $this->string(512),
            'type_id' => $this->bigInteger(),
            'is_pdv' => $this->boolean()->defaultValue(false),
            'activity_id' => $this->bigInteger(),
            'specific_reports' => $this->string(256),
            'report_date' => $this->integer(),
            'reminder' => $this->string(256),
            'pib' => $this->bigInteger(),
            'status' => $this->string(32)->defaultValue('new'),
        ]);
        $this->createIndex(
            'idx_company_name',
            Company::tableName(),
            'name',
            true
        );
        $this->createIndex(
            'idx_company_pib',
            Company::tableName(),
            'pib',
            true
        );
        $this->addForeignKey(
            'fk_company_type',
            Company::tableName(),
            'type_id',
            CompanyType::tableName(),
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_company_activities',
            Company::tableName(),
            'activity_id',
            CompanyActivities::tableName(),
            'id',
            'CASCADE'
        );

        $this->createTable(Customer::tableName(), [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'tg_id' => $this->bigInteger(),
            'company_id' => $this->bigInteger(),
            'firstname' => $this->string(32),
            'lastname' => $this->string(32),
            'username' => $this->string(32),
            'status' => $this->string(32)->defaultValue('new'),
        ]);
        $this->createIndex(
            'idx_customer_company_id',
            Customer::tableName(),
            'company_id'
        );
        $this->createIndex(
            'idx_customer_tg_id',
            Customer::tableName(),
            'tg_id',
            true
        );
        $this->createIndex(
            'idx_customer_username',
            Customer::tableName(),
            'username',
            true
        );

        $this->createTable(AccountantActivity::tableName(), [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'name' => $this->string(64),
            'due_date' => $this->integer(),
        ]);
        $this->createIndex(
            'idx_accountant_activity_name',
            AccountantActivity::tableName(),
            'name',
            true
        );
        $this->insert(AccountantActivity::tableName(), ['name' => 'knjiženje', 'due_date' => 5]);
        $this->insert(AccountantActivity::tableName(), ['name' => 'obračun zarada', 'due_date' => 2]);
        $this->insert(AccountantActivity::tableName(), ['name' => 'odgovor na pitanja vezanih za poslovanje firmi', 'due_date' => 5]);
        $this->insert(AccountantActivity::tableName(), ['name' => 'pdv', 'due_date' => 5]);
        $this->insert(AccountantActivity::tableName(), ['name' => 'plaćanje računa', 'due_date' => 2]);
        $this->insert(AccountantActivity::tableName(), ['name' => 'prijava/odjava radnika (pio/croso)', 'due_date' => 5]);
        $this->insert(AccountantActivity::tableName(), ['name' => 'promena podataka', 'due_date' => 6]);
        $this->insert(AccountantActivity::tableName(), ['name' => 'rad na terenu', 'due_date' => 5]);

        $this->createTable(Accountant::tableName(), [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'firstname' => $this->string(32),
            'lastname' => $this->string(32),
            'username' => $this->string(32),
            'status' => $this->string(32)->defaultValue('new'),
        ]);

        $this->createTable(CompanyAccountant::tableName(), [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'company_id' => $this->bigInteger(),
            'accountant_id' => $this->bigInteger(),
        ]);
        $this->createIndex(
            'idx_company_accountant_unique',
            CompanyAccountant::tableName(),
            ['company_id', 'accountant_id' ],
            true
        );
        $this->addForeignKey(
            'fk_company_accountant_company',
            CompanyAccountant::tableName(),
            'company_id',
            Company::tableName(),
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_company_accountant_accountant',
            CompanyAccountant::tableName(),
            'accountant_id',
            Accountant::tableName(),
            'id',
            'CASCADE'
        );

        $this->createTable(AccountantAccountantActivity::tableName(), [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'accountant_id' => $this->bigInteger(),
            'accountant_activity_id' => $this->bigInteger(),
        ]);
        $this->createIndex(
            'idx_accountant_activity_unique',
            AccountantAccountantActivity::tableName(),
            ['accountant_id', 'accountant_activity_id' ],
            true
        );
        $this->addForeignKey(
            'fk_accountant_activity_accountant',
            AccountantAccountantActivity::tableName(),
            'accountant_id',
            Accountant::tableName(),
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_accountant_activity_activity',
            AccountantAccountantActivity::tableName(),
            'accountant_activity_id',
            AccountantActivity::tableName(),
            'id',
            'CASCADE'
        );

        $this->createTable(ReminderTemplate::tableName(), [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'description' => $this->string(128),
            'text_ru' => $this->text(),
            'text_rs' => $this->text(),
        ]);

        $this->createTable(Reminder::tableName(), [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'company_id' => $this->bigInteger(),
            'template_id' => $this->bigInteger(),
            'send_date' => $this->integer(),
            'type' => $this->string(32),
            'message' => $this->string(512),
            'status' => $this->string(32)->defaultValue('new'),
        ]);
        $this->createIndex(
            'idx_reminder_company_id',
            Reminder::tableName(),
            'company_id'
        );

        $this->createTable(Event::tableName(), [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'company_id' => $this->bigInteger(),
            'topic' => $this->string(128),
            'details' => $this->string(128),
            'status' => $this->string(32)->defaultValue('new'),
        ]);
        $this->createIndex(
            'idx_event_company_id',
            Event::tableName(),
            'company_id'
        );

        $this->createTable(Task::tableName(), [
            'id' => 'BIGSERIAL PRIMARY KEY',//$this->primaryKey(),
            'company_id' => $this->bigInteger(),
            'category' => $this->string(64),
            'request' => $this->string(256),
            'due_date' => $this->integer(),
            'status' => $this->string(32)->defaultValue('new'),
        ]);
        $this->createIndex(
            'idx_task_company_id',
            Task::tableName(),
            'company_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_task_company_id', Task::tableName());
        $this->dropIndex('idx_event_company_id', Event::tableName());
        $this->dropIndex('idx_reminder_company_id', Reminder::tableName());
        $this->dropIndex('idx_accountant_activity_name', AccountantActivity::tableName());
        $this->dropIndex('idx_company_activities_name', CompanyActivities::tableName());
        $this->dropIndex('idx_company_type_name', CompanyType::tableName());
        $this->dropTable(Task::tableName());
        $this->dropTable(Event::tableName());
        $this->dropTable(Reminder::tableName());
        $this->dropTable(ReminderTemplate::tableName());
        $this->dropForeignKey('fk_accountant_activity_activity', AccountantAccountantActivity::tableName());
        $this->dropForeignKey('fk_accountant_activity_accountant', AccountantAccountantActivity::tableName());
        $this->dropTable(AccountantAccountantActivity::tableName());
        $this->dropForeignKey('fk_company_accountant_accountant', CompanyAccountant::tableName());
        $this->dropForeignKey('fk_company_accountant_company', CompanyAccountant::tableName());
        $this->dropTable(CompanyAccountant::tableName());
        $this->dropTable(Accountant::tableName());
        $this->dropTable(AccountantActivity::tableName());
        $this->dropForeignKey('fk_company_activities', Company::tableName());
        $this->dropForeignKey('fk_company_type', Company::tableName());
        $this->dropIndex('idx_customer_company_id', Customer::tableName());
        $this->dropIndex('idx_customer_username', Customer::tableName());
        $this->dropIndex('idx_customer_tg_id', Customer::tableName());
        $this->dropTable(Customer::tableName());
        $this->dropIndex('idx_company_pib', Company::tableName());
        $this->dropIndex('idx_company_name', Company::tableName());
        $this->dropTable(Company::tableName());
        $this->dropTable(CompanyActivities::tableName());
        $this->dropTable(CompanyType::tableName());
    }
}
