<?php

namespace app\controllers;

use yii\db\Query;
use app\models\Accountant;
use app\models\TaxCalendar;
use app\models\ReminderRegular;
use app\components\SettingsCalendarWidget;
use app\components\SettingsCalendarBodyWidget;
use app\components\ReminderCreateUpdateContentWidget;
use app\components\RegReminderTableRowWidget;
use app\models\ReminderSchedule;
use app\models\ReminderRegularCompany;
use app\services\CalendarService;

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
            $firstDay = date('Y-m-01');
            $lastDay = date('Y-m-t');
            $reminders = (new Query)
                ->select(['r.*'])
                ->from(['r' => ReminderRegular::tableName()])
                ->orderBy('r.deadline_day')
                ->all();
            $data = [
                'user' => $accountant,
                'reminders' => $reminders,
                'debug' => [
                    $firstDay,
                    $lastDay,
                ],
            ];
            return $this->renderPage($data, 'reg');
        } else {
            return $this->renderLogout();
        }
    }

    public function actionRegReminderCreate()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $reminder = new ReminderRegular();
            $html = ReminderCreateUpdateContentWidget::widget([
                'user' => $accountant,
                'reminder' => $reminder,
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

    public function actionRegReminderUpdate()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $reminder = ReminderRegular::findOne($id);
            if (!$reminder) {
                $reminder = new ReminderRegular();
            }
            $html = ReminderCreateUpdateContentWidget::widget([
                'user' => $accountant,
                'reminder' => $reminder,
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

    public function actionRegReminderSave()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $topicFrom = $request->post('topic');
            $textFrom = $request->post('text');
            $langFrom = $request->post('lang');
            $langTo = $langFrom == 'ru' ? 'rs' : 'ru';
            $data = [
                'text' => $topicFrom,
                'from' => $langFrom,
                'to' => $langTo,
            ];
            $topicTo = $this->makeN8nWebhookCall('translate', $data)['data']['translation'] ?? '';
            $data['text'] = $textFrom;
            $textTo = $this->makeN8nWebhookCall('translate', $data)['data']['translation'] ?? '';
            if ($langFrom == 'ru') {
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
            if ($data['id']) {
                $reminder = ReminderRegular::findOne($data['id']);
            } else {
                $reminder = new ReminderRegular();
            }
            $reminder->load($data, '');
            $reminder->save();
            $reminderSchedule = ReminderSchedule::find()
                ->where(['template_id' => $reminder->id, 'type' => 'regular'])
                ->all();
            $debug = [];
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
                    $item->message = ($lang['lang'] == 'ru') ? $reminder->text_ru : $reminder->text_rs;
                    $debug[] = [
                        'lang' => $lang,
                        'message' => $item->message,
                    ];
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
                $response->data = [
                    'status' => 'success',
                    'recievedData' => $request->post(),
                    'action' => $data['id'] ? 'updated' : 'created',
                    'reminder' => $reminder->toArray(),
                    'data' => RegReminderTableRowWidget::widget([
                        'user' => $accountant,
                        'reminder' => $reminder,
                        'class' => ['reg-reminder-btn'],
                    ]),
                    'debug' => $debug,
                ];
                return $response;
            }
        } else {
            return $this->renderLogout();
        }
    }

    public function actionCancelRegular()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $reminder = ReminderRegular::findOne($id);
            if ($reminder) {
                $reminder->delete();
                $response = \Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_JSON;
                $response->data = [
                    'status' => 'success',
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
            $reminder_id = $request->post('reminder_id');
            $schedule_id = $request->post('schedule_id');
            $company_id = $request->post('company_id');
            $reminder = ReminderSchedule::findOne([
                'id' => $schedule_id,
                'template_id' => $reminder_id,
                'company_id' => $company_id,
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
            $reminder_id = $request->post('reminder_id');
            $schedule_id = $request->post('schedule_id');
            $company_id = $request->post('company_id');
            $is_active = $request->post('is_active');
            if ($is_active) {
                // Activate reminder
                $reminder = ReminderRegular::findOne($reminder_id);
                if (!$reminder) {
                    $response->data = [
                        'status' => 'error',
                        'message' => 'Reminder template not found',
                    ];
                    return $response;
                }
                $reminderCompany = ReminderRegularCompany::findOne([
                    'company_id' => $company_id,
                    'reminder_id' => $reminder_id,
                ]);
                if (!$reminderCompany) {
                    $reminderCompany = new ReminderRegularCompany();
                    $reminderCompany->company_id = $company_id;
                    $reminderCompany->reminder_id = $reminder_id;
                    $reminderCompany->save();
                }
                $reminderSchedule = new ReminderSchedule();
                $reminderSchedule->deadline_date = date('Y-m-d', strtotime(date('Y-m-01') . ' + ' . ($reminder->deadline_day - 1) . ' days'));
                $reminderSchedule->escalation_date = CalendarService::getClosestWorkingDay(date('Y-m-d', strtotime($reminderSchedule->deadline_date . ' - 1 day')));
                $reminderSchedule->reminder_2_date = CalendarService::getClosestWorkingDay(date('Y-m-d', strtotime($reminderSchedule->escalation_date . ' - 1 day')));
                $reminderSchedule->reminder_1_date = CalendarService::getClosestWorkingDay(date('Y-m-d', strtotime($reminderSchedule->reminder_2_date . ' - 1 day')));
                $reminderSchedule->template_id = $reminder_id;
                $reminderSchedule->company_id = $company_id;
                $reminderSchedule->type = ReminderSchedule::TYPE_REGULAR;
                $reminderSchedule->target_month = date('Y-m') . '-01';
                $reminderSchedule->status = 'pending';
                $reminderSchedule->save();
                $response->data = [
                    'status' => 'success',
                    'message' => 'Reminder activated successfully',
                    'id' => $reminderSchedule->id,
                ];
            } else {
                // Deactivate reminder
                $reminderCompany = ReminderRegularCompany::findOne([
                    'company_id' => $company_id,
                    'reminder_id' => $reminder_id,
                ]);
                if ($reminderCompany) {
                    $reminderCompany->delete();
                }
                ReminderSchedule::deleteAll([
                    'template_id' => $reminder_id,
                    'company_id' => $company_id,
                    'type' => ReminderSchedule::TYPE_REGULAR,
                ]);
                $response->data = [
                    'status' => 'success',
                    'message' => 'Reminder deactivated successfully',
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
