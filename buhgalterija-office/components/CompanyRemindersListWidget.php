<?php

namespace app\components;

use app\models\Company;
use yii\db\Query;
use yii\db\Expression;
use app\models\ReminderRegular;
use app\models\ReminderRegularCompany;
use app\models\ReminderSchedule;
use app\models\TaxCalendar;
use yii\base\Widget;

class CompanyRemindersListWidget extends Widget
{
    public $user;
    public $company;

    public function run()
    {
        $on2Company = [
            ReminderRegularCompany::tableName() . '.reminder_id = ' . ReminderRegular::tableName() . '.id',
            ReminderRegularCompany::tableName() . '.company_id = ' . $this->company->id,
        ];
        $on2Schedule = [
            ReminderSchedule::tableName() . '.template_id = ' . ReminderRegular::tableName() . '.id',
            ReminderSchedule::tableName() . '.company_id = ' . $this->company->id,
            ReminderSchedule::tableName() . '.type = \'' . ReminderSchedule::TYPE_REGULAR . '\'',
            ReminderSchedule::tableName() . '.target_month = \'' . date('Y-m') . '-01\'',
        ];
        $regularRemindersQuery = (new Query())
            ->select([
                'reminder_id' => ReminderRegular::tableName() . '.id',
                'company_link_id' => ReminderRegularCompany::tableName() . '.id',
                'schedule_id' => ReminderSchedule::tableName() . '.id',
                ReminderRegular::tableName() . '.type_ru',
                ReminderRegular::tableName() . '.type_rs',
                ReminderRegular::tableName() . '.text_ru',
                ReminderRegular::tableName() . '.text_rs',
                ReminderRegular::tableName() . '.deadline_day',
                ReminderSchedule::tableName() . '.deadline_date',
                ReminderSchedule::tableName() . '.last_notified_type',
                ReminderSchedule::tableName() . '.status',
                'company_id' => new Expression($this->company->id),
                'type' => new Expression('\'rr\''),
            ])
            ->from(ReminderRegular::tableName())
            ->leftJoin(ReminderRegularCompany::tableName(), implode(' AND ', $on2Company))
            ->leftJoin(ReminderSchedule::tableName(), implode(' AND ', $on2Schedule))
            ->orderBy([ReminderSchedule::tableName() . '.deadline_date' => SORT_ASC]);

        $taxCalendarRemindersQuery = (new Query())
            ->select([
                'reminder_id' => TaxCalendar::tableName() . '.id',
                'schedule_id' => ReminderSchedule::tableName() . '.id',
                TaxCalendar::tableName() . '.activity_type_rs AS topic_rs',
                TaxCalendar::tableName() . '.activity_text_rs AS text_rs',
                TaxCalendar::tableName() . '.activity_type_ru AS topic_ru',
                TaxCalendar::tableName() . '.activity_text_ru AS text_ru',
                // TaxCalendar::tableName() . '.input_date AS deadline_date',
                'deadline_date' => new Expression('DATE(' . TaxCalendar::tableName() . '.input_date)'),
                'company_id' => new Expression($this->company->id),
                'type' => new Expression('\'tx\''),
            ])
            ->from(TaxCalendar::tableName())
            ->leftJoin(ReminderSchedule::tableName(), ReminderSchedule::tableName() . '.template_id = ' . TaxCalendar::tableName() . '.id')
            ->leftJoin(Company::tableName(), Company::tableName() . '.id = ' . ReminderSchedule::tableName() . '.company_id')
            ->where([TaxCalendar::tableName() . '.target_month' => date('Y-m') . '-01'])
            ->orderBy([ReminderSchedule::tableName() . '.deadline_date' => SORT_ASC]);
        return $this->render('companyreminderslist', [
            'user' => $this->user,
            'company' => $this->company,
            'regReminders' => $regularRemindersQuery->all(),
            'taxCalendarReminders' => $taxCalendarRemindersQuery->all(),
        ]);
    }
}
