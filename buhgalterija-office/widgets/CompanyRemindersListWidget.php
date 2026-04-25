<?php

namespace app\widgets;

use app\models\Company;
use app\models\ReminderOneTime;
use app\models\ReminderOnetimeCompany;
use yii\db\Query;
use yii\db\Expression;
use app\models\ReminderRegular;
use app\models\ReminderRegularCompany;
use app\models\ReminderSchedule;
use app\models\ReminderYearly;
use app\models\ReminderYearlyCompany;
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
            ->orderBy([ReminderRegular::tableName() . '.deadline_day' => SORT_ASC]);

        $on2Company = [
            ReminderYearlyCompany::tableName() . '.reminder_id = ' . ReminderYearly::tableName() . '.id',
            ReminderYearlyCompany::tableName() . '.company_id = ' . $this->company->id,
        ];
        $on2Schedule = [
            ReminderSchedule::tableName() . '.template_id = ' . ReminderYearly::tableName() . '.id',
            ReminderSchedule::tableName() . '.company_id = ' . $this->company->id,
            ReminderSchedule::tableName() . '.type = \'' . ReminderSchedule::TYPE_YEARLY . '\'',
            ReminderSchedule::tableName() . '.target_month = \'' . date('Y-m') . '-01\'',
        ];
        $yearlyRemindersQuery = (new Query())
            ->select([
                'reminder_id' => ReminderYearly::tableName() . '.id',
                'company_link_id' => ReminderYearlyCompany::tableName() . '.id',
                'schedule_id' => ReminderSchedule::tableName() . '.id',
                ReminderYearly::tableName() . '.type_ru',
                ReminderYearly::tableName() . '.type_rs',
                ReminderYearly::tableName() . '.text_ru',
                ReminderYearly::tableName() . '.text_rs',
                ReminderYearly::tableName() . '.deadline_month',
                ReminderYearly::tableName() . '.deadline_day',
                ReminderSchedule::tableName() . '.deadline_date',
                ReminderSchedule::tableName() . '.last_notified_type',
                ReminderSchedule::tableName() . '.status',
                'company_id' => new Expression($this->company->id),
                'type' => new Expression('\'rr\''),
            ])
            ->from(ReminderYearly::tableName())
            ->leftJoin(ReminderYearlyCompany::tableName(), implode(' AND ', $on2Company))
            ->leftJoin(ReminderSchedule::tableName(), implode(' AND ', $on2Schedule))
            ->orderBy([ReminderYearly::tableName() . '.deadline_month' => SORT_ASC, ReminderYearly::tableName() . '.deadline_day' => SORT_ASC]);

        $on2Company = [
            ReminderOnetimeCompany::tableName() . '.reminder_id = ' . ReminderOneTime::tableName() . '.id',
            ReminderOnetimeCompany::tableName() . '.company_id = ' . $this->company->id,
        ];
        $on2Schedule = [
            ReminderSchedule::tableName() . '.template_id = ' . ReminderOneTime::tableName() . '.id',
            ReminderSchedule::tableName() . '.company_id = ' . $this->company->id,
            ReminderSchedule::tableName() . '.type = \'' . ReminderSchedule::TYPE_ONE_TIME . '\'',
            ReminderSchedule::tableName() . '.target_month = \'' . date('Y-m') . '-01\'',
        ];
        $start = date('Y-m-01');
        $end   = date('Y-m-t');
        $onetimeRemindersQuery = (new Query())
            ->select([
                'reminder_id' => ReminderOneTime::tableName() . '.id',
                'company_link_id' => ReminderOnetimeCompany::tableName() . '.id',
                'schedule_id' => ReminderSchedule::tableName() . '.id',
                ReminderOneTime::tableName() . '.type_ru',
                ReminderOneTime::tableName() . '.type_rs',
                ReminderOneTime::tableName() . '.text_ru',
                ReminderOneTime::tableName() . '.text_rs',
                ReminderOneTime::tableName() . '.deadline',
                ReminderSchedule::tableName() . '.deadline_date',
                ReminderSchedule::tableName() . '.last_notified_type',
                ReminderSchedule::tableName() . '.status',
                'company_id' => new Expression($this->company->id),
                'type' => new Expression('\'rr\''),
            ])
            ->from(ReminderOneTime::tableName())
            ->leftJoin(ReminderOnetimeCompany::tableName(), implode(' AND ', $on2Company))
            ->leftJoin(ReminderSchedule::tableName(), implode(' AND ', $on2Schedule))
            ->where(['between', 'deadline', $start, $end])
            ->orderBy([ReminderOneTime::tableName() . '.deadline' => SORT_ASC]);

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
            'yearlyReminders' => $yearlyRemindersQuery->all(),
            'onetimeReminders' => $onetimeRemindersQuery->all(),
            'taxCalendarReminders' => $taxCalendarRemindersQuery->all(),
        ]);
    }
}
