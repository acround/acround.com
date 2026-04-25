<?php

namespace app\controllers;

use yii\db\Query;
use app\widgets\ReminderCreateUpdateContentWidget;
use app\widgets\ReminderOneTimeTableRowWidget;
use app\widgets\ReminderRegTableRowWidget;
use app\widgets\ReminderYearlyTableRowWidget;
use app\widgets\SettingsCalendarBodyWidget;
use app\widgets\SettingsCalendarWidget;
use app\models\Accountant;
use app\models\Company;
use app\models\ReminderOneTime;
use app\models\ReminderOnetimeCompany;
use app\models\ReminderRegular;
use app\models\ReminderRegularCompany;
use app\models\ReminderSchedule;
use app\models\ReminderYearly;
use app\models\ReminderYearlyCompany;
use app\models\TaxCalendar;
use app\services\CalendarService;
use app\services\DictionaryService;

class ReminderController extends BaseController
{

    public function actionCancel()
    {
        return $this->render('cancel');
    }

    private function getDataToPage(Accountant $accountant, $month)
    {
        $monthsQuery = (new Query())
            ->select(['month' => 'EXTRACT(MONTH FROM input_date)', 'year' => 'EXTRACT(YEAR FROM input_date)'])
            ->distinct()
            ->from(TaxCalendar::tableName())
            ->orderBy(['year' => SORT_DESC, 'month' => SORT_DESC]);
        $monthsRaw = $monthsQuery->all();
        $monthList = [];
        foreach ($monthsRaw as $value) {
            if ($value['month'] < 10) {
                $value['month'] = '0' . $value['month'];
            }
            $monthList[] = $value['year'] . '-' . $value['month'];
        }
        if (!in_array($month, $monthList) && count($monthList) > 0) {
            $month = $monthList[0];
        }
        $firstDay = date('Y-m-01', strtotime($month));
        $lastDay = date('Y-m-t', strtotime($month));
        $taxCalendar = TaxCalendar::find()
            ->where(['between', 'input_date', $firstDay, $lastDay])
            ->orderBy(["input_date" => SORT_ASC, 'activity_type_rs' => SORT_ASC])
            ->all();
        $data = [
            'user' => $accountant,
            'taxCalendar' => $taxCalendar,
            'month' => date('m', strtotime($month)),
            'year' => date('Y', strtotime($month)),
            'monthList' => $monthList,
        ];
        return $data;
    }

    public function actionPage()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $month = date('Y-m');
            $data = $this->getDataToPage($accountant, $month);
            return $this->renderPage($data);
        } else {
            return $this->renderLogout();
        }
    }

    public function actionReg()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $reminders = (new Query)
                ->select(['r.*'])
                ->from(['r' => ReminderRegular::tableName()])
                ->orderBy('r.deadline_day')
                ->all();
            $data = [
                'user' => $accountant,
                'reminders' => $reminders,
            ];
            return $this->renderPage($data, 'reg');
        } else {
            return $this->renderLogout();
        }
    }

    public function actionReminderCreate()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $type = $request->post('reminderType');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            switch ($type) {
                case 'regular':
                    $reminder = new ReminderRegular();
                    break;
                case 'yearly':
                    $reminder = new ReminderYearly();
                    break;
                case 'one-time':
                    $reminder = new ReminderOneTime();
                    break;
                default:
                    $reminder = new ReminderRegular();
            }
            $html = ReminderCreateUpdateContentWidget::widget([
                'user' => $accountant,
                'reminder' => $reminder,
                'type' => $type,
            ]);
            $response = \Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = [
                'status' => 'success',
                'message' => 'Reminder created successfully',
                'data' => $html,
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionReminderUpdate()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $type = $request->post('reminderType');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            switch ($type) {
                case 'regular':
                    $reminder = ReminderRegular::findOne($id);
                    break;
                case 'yearly':
                    $reminder = ReminderYearly::findOne($id);
                    break;
                case 'one-time':
                    $reminder = ReminderOneTime::findOne($id);
                    break;
                default:
                    $reminder = ReminderRegular::findOne($id);
            }

            if (!$reminder) {
                switch ($type) {
                    case 'regular':
                        $reminder = new ReminderRegular();
                        break;
                    case 'yearly':
                        $reminder = new ReminderYearly();
                        break;
                    case 'one-time':
                        $reminder = new ReminderOneTime();
                        break;
                    default:
                        $reminder = new ReminderRegular();
                }
            }
            $html = ReminderCreateUpdateContentWidget::widget([
                'user' => $accountant,
                'reminder' => $reminder,
                'type' => $type,
            ]);
            $response = \Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = [
                'status' => 'success',
                // 'message' => 'Reminder updated successfully',
                'data' => $html,
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionReminderSave()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $type = $request->post('reminderType');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $topicFrom = $request->post('topic');
            $textFrom = $request->post('text');
            $langFrom = $request->post('lang');
            $langTo = $langFrom == DictionaryService::LANG_RUSSIAN ? DictionaryService::LANG_SERBIAN : DictionaryService::LANG_RUSSIAN;
            $data = [
                'text' => $topicFrom,
                'from' => $langFrom,
                'to' => $langTo,
            ];
            $topicTo = $this->makeN8nWebhookCall('translate', $data)['data']['translation'] ?? '';
            $data['text'] = $textFrom;
            $textTo = $this->makeN8nWebhookCall('translate', $data)['data']['translation'] ?? '';
            if ($langFrom == DictionaryService::LANG_RUSSIAN) {
                $topicRu = $topicFrom;
                $textRu = $textFrom;
                $topicRs = $topicTo;
                $textRs = $textTo;
            } else {
                $topicRs = $topicFrom;
                $textRs = $textFrom;
                $topicRu = $topicTo;
                $textRu = $textTo;
            }
            $data = [
                'id' => $request->post('id'),
                'deadline_day' => $request->post('deadLine'),
                'type_ru' => $topicRu,
                'type_rs' => $topicRs,
                'text_ru' => $textRu,
                'text_rs' => $textRs,
            ];
            $id = $data['id'];
            if ($data['id']) {
                switch ($type) {
                    case 'regular':
                        $reminder = ReminderRegular::findOne($id);
                        break;
                    case 'yearly':
                        $reminder = ReminderYearly::findOne($id);
                        break;
                    case 'one-time':
                        $reminder = ReminderOneTime::findOne($id);
                        break;
                    default:
                        $reminder = ReminderRegular::findOne($id);
                }
            } else {
                switch ($type) {
                    case 'regular':
                        $reminder = new ReminderRegular();
                        break;
                    case 'yearly':
                        $reminder = new ReminderYearly();
                        break;
                    case 'one-time':
                        $reminder = new ReminderOneTime();
                        break;
                    default:
                        $reminder = new ReminderRegular();
                }
            }
            switch ($type) {
                case 'regular':
                    break;
                case 'yearly':
                    $deadlineDay = explode('-', $data['deadline_day']);
                    $data['deadline_day'] = $deadlineDay[2];
                    $data['deadline_month'] = $deadlineDay[1];
                    break;
                case 'one-time':
                    $data['deadline'] = $data['deadline_day'];
                    unset($data['deadline_day']);
                    break;
            }
            $reminder->load($data, '');
            $reminder->save();
            $reminderSchedule = ReminderSchedule::find()
                ->where(['template_id' => $reminder->id, 'type' => $type])
                ->all();
            // $debug = [
            //     'reminderClass' => get_class($reminder),
            // ];
            if ($reminderSchedule) {
                foreach ($reminderSchedule as $item) {
                    $companyId = $item->company_id;
                    $lang = (new Query())
                        ->select([
                            'lang' => "coalesce(c2.lang, 'no')",
                            'tg_id' => 'coalesce(c2.tg_id, 0)'
                        ])
                        ->from(['c' => 'company'])
                        ->leftJoin(['cc' => 'company_customer'], 'cc.company_id = c.id')
                        ->leftJoin(['c2' => 'customer'], 'c2.id = cc.customer_id')
                        ->where(['c.id' => $companyId])
                        ->limit(1)
                        ->one();
                    if ($lang['tg_id'] == 0 || $lang['tg_id'] == null) {
                        continue;
                    }
                    $item->message = ($lang['lang'] == DictionaryService::LANG_RUSSIAN) ? $reminder->text_ru : $reminder->text_rs;
                    // $debug[] = [
                    //     'lang' => $lang,
                    //     'message' => $item->message,
                    // ];
                    $item->save();
                }
            }
            $response = \Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            if ($reminder->hasErrors()) {
                $response->data = [
                    'status' => 'error',
                    'message' => 'Validation errors',
                    'errors' => $reminder->getErrors(),
                ];
                return $response;
            } else {
                switch ($type) {
                    case 'regular':
                        $out = ReminderRegTableRowWidget::widget([
                            'user' => $accountant,
                            'reminder' => $reminder,
                            'class' => ['reg-reminder-btn'],
                        ]);
                        break;
                    case 'yearly':
                        $out = ReminderYearlyTableRowWidget::widget([
                            'user' => $accountant,
                            'reminder' => $reminder,
                            'class' => ['yearly-reminder-btn'],
                        ]);
                        break;
                    case 'one-time':
                        $out = ReminderOneTimeTableRowWidget::widget([
                            'user' => $accountant,
                            'reminder' => $reminder,
                            'class' => ['one-time-reminder-btn'],
                        ]);
                        break;
                }
                $response->data = [
                    'status' => 'success',
                    // 'recievedData' => $request->post(),
                    'action' => $data['id'] ? 'updated' : 'created',
                    'reminder' => $reminder->toArray(),
                    'data' => $out,
                    // 'debug' => $debug,
                ];
                return $response;
            }
        } else {
            return $this->renderLogout();
        }
    }

    public function actionCancelReminder()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $type = $request->post('type');
            switch ($type) {
                case 'regular':
                    $reminder = ReminderRegular::findOne($id);
                    break;
                case 'yearly':
                    $reminder = ReminderYearly::findOne($id);
                    break;
                case 'one-time':
                    $reminder = ReminderOneTime::findOne($id);
                    break;
                    // default:
                    //     $reminder = ReminderRegular::findOne($id);
            }
            // $reminder = ReminderRegular::findOne($id);
            if ($reminder) {
                $r = $reminder->delete();
                $response = \Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_JSON;
                $response->data = [
                    'status' => 'success',
                    'r' => $r,
                    'errors' => $reminder->getErrors(),
                    'class' => get_class($reminder),
                    'reminder' => $reminder,
                ];
                return $response;
            } else {
                $response = \Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_JSON;
                $response->data = [
                    'status' => 'error',
                    'message' => 'Reminder not found',
                ];
                return $response;
            }
        } else {
            return $this->renderLogout();
        }
    }

    public function actionYearly()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $reminders = ReminderYearly::find()->orderBy(['deadline_month' => SORT_ASC, 'deadline_day' => SORT_ASC])->all();
            $data = [
                'user' => $accountant,
                'reminders' => $reminders,
            ];
            return $this->renderPage($data, 'yearly');
        } else {
            return $this->renderLogout();
        }
    }

    public function actionOneTime()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $remindersQuery = ReminderOneTime::find()->where(['>', 'deadline', date('Y-m-d')])->orderBy(['deadline' => SORT_ASC]);
            $reminders = $remindersQuery->all();
            $data = [
                'user' => $accountant,
                'reminders' => $reminders,
                'debug' => $remindersQuery->createCommand()->getRawSql(),
            ];
            return $this->renderPage($data, 'one-time');
        } else {
            return $this->renderLogout();
        }
    }

    public function actionTaxCalendar($month = null)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            if (!$month) {
                $month = date('Y-m');
            }
            $data = $this->getDataToPage($accountant, $month);
            $html = SettingsCalendarWidget::widget($data);
            $response = \Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = [
                'status' => 'success',
                'data' => $html,
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionTaxCalendarTable($month = null)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            if (!$month) {
                $month = date('Y-m');
            }
            $data = $this->getDataToPage($accountant, $month);
            $html = SettingsCalendarBodyWidget::widget([
                'taxCalendar' => $data['taxCalendar'],
                'user' => $accountant,
            ]);
            $response = \Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = [
                'status' => 'success',
                'data' => $html,
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionTaxCalendarMonthLoad()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $response = \Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            list($year, $month) = explode('-', $request->post('month'));
            $data = [
                'year' => $year,
                'month' => $month,
            ];
            $out = $this->makeN8nWebhookCall('tax-calendar-month-load', $data);
            if ($out && isset($out['status']) && $out['status'] == 'success' && $out['code'] == 200) {
                $response->data = [
                    'status' => 'success',
                    'data' => $out,
                ];
                return $response;
            } else {
                $response->data = $out;
                return $response;
            }
        } else {
            return $this->renderLogout();
        }
    }

    public function actionStopReminder()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $type = $request->post('type');
            $reminder_id = $request->post('reminder_id');
            $schedule_id = $request->post('schedule_id');
            $company_id = $request->post('company_id');
            $reminder = ReminderSchedule::findOne([
                'id' => $schedule_id,
                'template_id' => $reminder_id,
                'company_id' => $company_id,
                'type' => $type,
            ]);
            if ($reminder) {
                $reminder->status = 'stopped';
                $reminder->save();
                $response->data = [
                    'status' => 'success',
                    'message' => 'Reminder stopped successfully',
                ];
            } else {
                $response->data = [
                    'status' => 'error',
                    'message' => 'Reminder not found',
                ];
            }
        } else {
            $response->data = [
                'status' => 'logout',
                'message' => 'Invalid token',
            ];
        }
        return $response;
    }

    public function actionToggleReminderActivity()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $type = $request->post('type');
            $reminder_id = $request->post('reminder_id');
            $company_id = $request->post('company_id');
            $company = Company::find()->where(['id' => $company_id,])->one();
            $customer = $company->getCustomer();
            $lang = $customer ? $customer->lang : DictionaryService::LANG_DEFAULT;
            $is_active = $request->post('is_active');
            switch ($type) {
                case 'regular':
                    $reminderClass = ReminderRegular::class;
                    $reminderCompanyClass = ReminderRegularCompany::class;
                    break;
                case 'yearly':
                    $reminderClass = ReminderYearly::class;
                    $reminderCompanyClass = ReminderYearlyCompany::class;
                    break;
                case 'one-time':
                    $reminderClass = ReminderOneTime::class;
                    $reminderCompanyClass = ReminderOnetimeCompany::class;
                    break;
                default:
                    $reminderClass = ReminderRegular::class;
                    $reminderCompanyClass = ReminderRegularCompany::class;
            }
            if ($is_active) {
                // Activate reminder
                $reminder = $reminderClass::findOne($reminder_id);
                if (!$reminder) {
                    $response->data = [
                        'status' => 'error',
                        'message' => 'Reminder template not found',
                    ];
                    return $response;
                }
                $reminderCompany = $reminderCompanyClass::findOne([
                    'company_id' => $company_id,
                    'reminder_id' => $reminder_id,
                ]);
                if (!$reminderCompany) {
                    $reminderCompany = new $reminderCompanyClass();
                    $reminderCompany->company_id = $company_id;
                    $reminderCompany->reminder_id = $reminder_id;
                    $reminderCompany->save();
                }
                switch ($type) {
                    case 'regular':
                        $deadlineDate = date('Y-m-') . str_pad($reminder->deadline_day, 2, '0', STR_PAD_LEFT);
                        break;
                    case 'yearly':
                        $deadlineDate = date('Y-') .  str_pad($reminder->deadline_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($reminder->deadline_day, 2, '0', STR_PAD_LEFT);
                        break;
                    case 'one-time':
                        $deadlineDate = $reminder->deadline;
                        break;
                }
                $reminderSchedule = new ReminderSchedule();
                $reminderSchedule->deadline_date = date('Y-m-d', strtotime($deadlineDate));
                $reminderSchedule->escalation_date = CalendarService::getClosestWorkingDay(date('Y-m-d', strtotime($reminderSchedule->deadline_date . ' - 1 day')));
                $reminderSchedule->reminder_2_date = CalendarService::getClosestWorkingDay(date('Y-m-d', strtotime($reminderSchedule->escalation_date . ' - 1 day')));
                $reminderSchedule->reminder_1_date = CalendarService::getClosestWorkingDay(date('Y-m-d', strtotime($reminderSchedule->reminder_2_date . ' - 1 day')));
                $reminderSchedule->template_id = $reminder_id;
                $reminderSchedule->company_id = $company_id;
                $reminderSchedule->type = $type;
                $reminderSchedule->target_month = date('Y-m') . '-01';
                $reminderSchedule->message = ($lang == DictionaryService::LANG_RUSSIAN) ? $reminder->text_ru : $reminder->text_rs;
                $reminderSchedule->status = 'pending';
                $reminderSchedule->save();
                $response->data = [
                    'status' => 'success',
                    'message' => 'Reminder activated successfully',
                    'id' => $reminderSchedule->id,
                    'company' => $company,
                    'customer' => $customer,
                    'lang' => $lang,
                ];
            } else {
                // Deactivate reminder
                $reminderCompany = $reminderCompanyClass::findOne([
                    'company_id' => $company_id,
                    'reminder_id' => $reminder_id,
                ]);
                if ($reminderCompany) {
                    $reminderCompany->delete();
                }
                ReminderSchedule::deleteAll([
                    'template_id' => $reminder_id,
                    'company_id' => $company_id,
                    'type' => $type,
                ]);
                $response->data = [
                    'status' => 'success',
                    'message' => 'Reminder deactivated successfully',
                    'lang' => $lang,
                ];
            }
        } else {
            $response->data = [
                'status' => 'logout',
                'message' => 'Invalid token',
            ];
        }
        return $response;
    }

    public function actionToggleTaxActivity()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $reminder_id = $request->post('reminder_id');
            $schedule_id = $request->post('schedule_id');
            $company_id = $request->post('company_id');
            $is_active = $request->post('is_active');
            if ($is_active) {
                $reminder = TaxCalendar::findOne($reminder_id);
                if (!$reminder) {
                    $response->data = [
                        'status' => 'error',
                        'message' => 'Reminder template not found',
                    ];
                    return $response;
                }
                $reminderSchedule = new ReminderSchedule();
                $reminderSchedule->deadline_date = $reminder->input_date;
                $reminderSchedule->escalation_date = CalendarService::getClosestWorkingDay(date('Y-m-d', strtotime($reminderSchedule->deadline_date . ' - 1 day')));
                $reminderSchedule->reminder_2_date = CalendarService::getClosestWorkingDay(date('Y-m-d', strtotime($reminderSchedule->escalation_date . ' - 1 day')));
                $reminderSchedule->reminder_1_date = CalendarService::getClosestWorkingDay(date('Y-m-d', strtotime($reminderSchedule->reminder_2_date . ' - 1 day')));
                $reminderSchedule->template_id = $reminder_id;
                $reminderSchedule->company_id = $company_id;
                $reminderSchedule->type = ReminderSchedule::TYPE_TAX_CALENDAR;
                $reminderSchedule->target_month = date('Y-m') . '-01';
                $reminderSchedule->status = 'pending';
                $reminderSchedule->save();
                $response->data = [
                    'status' => 'success',
                    'message' => 'Reminder activated successfully',
                    'id' => $reminderSchedule->id,
                ];
            } else {
                $reminderSchedule = ReminderSchedule::findOne($schedule_id);
                if ($reminderSchedule) {
                    $reminderSchedule->delete();
                    $response->data = [
                        'status' => 'success',
                        'message' => 'Reminder deactivated successfully',
                    ];
                } else {
                    $response->data = [
                        'status' => 'error',
                        'message' => 'Reminder not found',
                    ];
                }
            }
        } else {
            $response->data = [
                'status' => 'logout',
                'message' => 'Invalid token',
            ];
        }
        return $response;
    }
}
