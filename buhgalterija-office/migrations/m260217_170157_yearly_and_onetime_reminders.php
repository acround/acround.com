<?php

use yii\db\Migration;

class m260217_170157_yearly_and_onetime_reminders extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('reminder_yearly', [
            'id' => $this->primaryKey(),
            'deadline_day' => $this->integer()->notNull(),
            'deadline_month' => $this->integer()->notNull(),
            'type_ru' => $this->string(64),
            'type_rs' => $this->string(64),
            'text_ru' => $this->string(1024),
            'text_rs' => $this->string(1024),
        ]);

        $this->createTable('reminder_onetime', [
            'id' => $this->primaryKey(),
            'deadline' => $this->date()->notNull(),
            'type_ru' => $this->string(64),
            'type_rs' => $this->string(64),
            'text_ru' => $this->string(1024),
            'text_rs' => $this->string(1024),
        ]);

        $this->createTable('reminder_yearly_company', [
            'id' => $this->primaryKey(),
            'reminder_id' => $this->bigInteger()->notNull(),
            'company_id' => $this->bigInteger()->notNull(),
        ]);

        $this->createTable('reminder_onetime_company', [
            'id' => $this->primaryKey(),
            'reminder_id' => $this->bigInteger()->notNull(),
            'company_id' => $this->bigInteger()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-reminder_onetime_company-reminder_id',
            'reminder_onetime_company',
            'reminder_id',
            'reminder_onetime',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-reminder_onetime_company-company_id',
            'reminder_onetime_company',
            'company_id',
            'company',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-reminder_yearly_company-reminder_id',
            'reminder_yearly_company',
            'reminder_id',
            'reminder_yearly',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-reminder_yearly_company-company_id',
            'reminder_yearly_company',
            'company_id',
            'company',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-reminder_onetime_company-reminder_id', 'reminder_onetime_company');
        $this->dropForeignKey('fk-reminder_onetime_company-company_id', 'reminder_onetime_company');
        $this->dropForeignKey('fk-reminder_yearly_company-reminder_id', 'reminder_yearly_company');
        $this->dropForeignKey('fk-reminder_yearly_company-company_id', 'reminder_yearly_company');
        $this->dropTable('reminder_yearly_company');
        $this->dropTable('reminder_onetime_company');
        $this->dropTable('reminder_yearly');
        $this->dropTable('reminder_onetime');
    }
}
