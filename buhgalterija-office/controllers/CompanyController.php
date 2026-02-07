<?php

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\web\Response;
use app\components\CompanyListWidget;
use app\components\CompanyNotesWidget;
use app\controllers\BaseController;
use app\models\Accountant;
use app\models\Company;
use app\models\CompanyAccountant;
use app\models\CompanyActivities;
use app\models\CompanyType;
use app\models\Customer;
use app\models\Document;
use app\models\Reminder;
use app\models\ReminderRegular;
use app\models\ReminderRegularCompany;
use app\models\ReminderSchedule;
use app\models\Task;
use app\models\TaskDocument;
use app\models\TaxCalendar;
use app\services\CalendarService;

class CompanyController extends BaseController
{
    public static $statuses = [
        'statusActive',
        'statusOnboarding',
        'statusPaused',
        'statusInactive',
    ];

    protected function getDataForPage($accountant, $status = null, $filters = [])
    {
        $select = [
            'c.id AS company_id',
            'c.name AS company_name',
            'ct.name AS company_type',
            'ca.name AS company_activity',
            'c.is_pdv',
            'c.pib',
            'c.status AS company_status',
            'COUNT("to".id) AS overdue',
            'COUNT("tp".id) AS openTasks',
        ];
        $sort = $filters['sort'] ?? 'name';
        $companiesQuery = (new Query())
            ->select($select)
            ->distinct()
            ->from(['c' => 'company'])
            ->leftJoin(['ct' => 'company_type'], 'ct.id = c.type_id')
            ->leftJoin(['ca' => 'company_activities'], 'ca.id = c.activity_id')
            ->leftJoin(['tp' => Task::tableName()], '"tp".company_id = c.id AND "tp".status = \'inProgress\'')
            ->leftJoin(['to' => Task::tableName()], '"to".company_id = c.id AND "to".status = \'overdue\'');

        if ($filters['name'] ?? null) {
            $companiesQuery->andWhere(['LIKE', 'c.name', $filters['name']]);
        }
        if ($filters['status'] ?? null) {
            $companiesQuery->andWhere(['c.status' => $status]);
        }
        if ($filters['accountant'] ?? null) {
            $companiesQuery->innerJoin(['cc' => CompanyAccountant::tableName()], 'cc.company_id = c.id');
            $companiesQuery->andWhere(['cc.accountant_id' => $filters['accountant']]);
        }

        switch ($sort) {
            case 'name':
                $companiesQuery->orderBy(['c.name' => SORT_ASC]);
                break;
            case 'overdue':
                $companiesQuery
                    ->having(['>', 'COUNT("to".id)', 0])
                    ->orderBy(['overdue' => SORT_DESC]);
                break;
            case 'openTasks':
                $companiesQuery
                    ->having(['>', 'COUNT("tp".id)', 0])
                    ->orderBy(['openTasks' => SORT_DESC]);
                break;
        }

        $companiesQuery->groupBy([
            'c.id',
            'c.name',
            'ct.name',
            'ca.name',
            'c.is_pdv',
            'c.pib',
            'c.status',
        ]);

        $companies = $companiesQuery->all();

        $filterStatus = (new Query())
            ->select('status')
            ->distinct()
            ->from(['c' => Company::tableName()])
            ->orderBy('status')
            ->all();
        $filterAccountantQuery = (new Query())
            ->select(['a.id', 'a.firstname', 'a.lastname'])
            ->distinct()
            ->from(['c' => Company::tableName()])
            ->innerJoin(['ca' => CompanyAccountant::tableName()], 'ca.company_id = c.id')
            ->innerJoin(['a' => Accountant::tableName()], 'a.id = ca.accountant_id');
        $data = [
            'user' => $accountant,
            'companies' => $companies,
            'back' => $status !== null,
            'filterStatus' => $filterStatus,
            'filterAccountant' => $filterAccountantQuery->all(),
        ];
        return $data;
    }

    public function actionPage($status = null)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $data = $this->getDataForPage($accountant, $status);
            return $this->renderPage($data);
        } else {
            return $this->renderLogout();
        }
    }

    public function actionFilter($pageStatus = null)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $filters = [
                'name' => $request->post('name'),
                'status' => $request->post('status'),
                'accountant' => $request->post('accountant'),
                'sort' => $request->post('sort'),
            ];
            $data = $this->getDataForPage($accountant, $pageStatus, $filters);

            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'data' => CompanyListWidget::widget(['user' => $accountant, 'companies' => $data['companies']]),
                // 'filters' => $filters,
                // 'debug' => $data,
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionProfile()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $company = Company::findOne(['id' => $id]);
            $customers = $company->getCustomers();
            $activity = CompanyActivities::findOne(['id' => $company->activity_id]);
            $taskQuery = Task::find()
                ->where(['company_id' => $company->id]);
            if ($accountant->rule !== 'ceo') {
                $taskQuery->andWhere(['accountant_id' => $accountant->id]);
            }
            $taskCount = $taskQuery->count();
            $tasks = $taskQuery
                ->orderBy('due_date ASC')
                ->all();
            $tasksOverdue = $taskQuery
                ->andWhere(['status' => 'overdue'])
                ->count();
            $documentsQuery = Document::find()
                ->from(['d' => Document::tableName()])
                ->where(['d.company_id' => $id]);
            if ($accountant->rule !== 'ceo') {
                $documentsQuery
                    ->leftJoin(['td' => TaskDocument::tableName()], 'td.document_id = d.id')
                    ->leftJoin(['t' => Task::tableName()], 't.id = td.task_id')
                    ->andWhere(['t.accountant_id' => $accountant->id]);
            }
            $documents = $documentsQuery
                ->orderBy('d.created_at DESC')
                ->all();
            $documentsCount = $documentsQuery->count();
            $data = [
                'user' => $accountant,
                'company' => $company,
                'activity' => $activity,
                'customers' => $customers,
                'taskCount' => $taskCount,
                'tasks' => $tasks,
                'tasksOverdue' => $tasksOverdue,
                'documents' => $documents,
                'documentsCount' => $documentsCount,
            ];

            return $this->renderPage($data, 'profile');
        } else {
            return $this->renderLogout();
        }
    }

    public function actionEdit()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $company = $id ? Company::findOne(['id' => $id]) : (new Company());
            $companyTypes = CompanyType::find()->all();
            $companyStatuses = Company::$statuses;
            $companySector = CompanyActivities::find()->all();
            $accountants = Accountant::find()->where(['!=', 'rule', 'bot'])->orderBy(['lastname' => SORT_ASC, 'firstname' => SORT_ASC,])->all();
            $response->data = [
                'status' => 'success',
                'data' => $this->renderPartial('edit', [
                    'user' => $accountant,
                    'company' => $company,
                    'companyTypes' => $companyTypes,
                    'companyStatuses' => $companyStatuses,
                    'companySector' => $companySector,
                    'accountants' => $accountants,
                ]),
                'company' => $company,
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionSave()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $company = $id ? Company::findOne(['id' => $id]) : (new Company());
            $company->name = $request->post('name');
            $company->name_tg = $request->post('name_tg');
            $company->type_id = $request->post('type_id');
            $company->activity_id = $request->post('activity_id');
            $company->pib = $request->post('pib');
            $company->is_pdv = $request->post('is_pdv');
            $company->status = $request->post('status');
            $company->save();
            $companyAccountant = $request->post('accountant_id');
            $errors = [];
            if ($companyAccountant) {
                $ca = CompanyAccountant::findOne(['company_id' => $company->id]);
                if (!$ca) {
                    $ca = new CompanyAccountant();
                    $ca->company_id = $company->id;
                    $ca->accountant_id = $companyAccountant;
                    $ca->save();
                    $errors = $ca->getErrors();
                    $p = 1;
                } elseif ($ca->accountant_id !== $companyAccountant) {
                    $ca->accountant_id = $companyAccountant;
                    $p = 2;
                    $ca->save();
                    $errors = $ca->getErrors();
                }
            } else {
                $ca = CompanyAccountant::findOne(['company_id' => $company->id]);
                if ($ca) {
                    $ca->delete();
                    $errors = $ca->getErrors();
                    $p = 3;
                } else {
                    $p = 4;
                }
            }
            $response->data = [
                'status' => 'success',
                'id' => $company->id,
                'company' => $company,
                'errors' => $errors,
                'p' => $p
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionAddNote()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $companyId = $request->post('company_id');
        $company = Company::findOne(['id' => $companyId]);
        $noteText = $request->post('note_text');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $accountant = $accountant;
            if ($companyId && $noteText) {
                $note = new \app\models\CompanyNotes();
                $note->company_id = $companyId;
                $note->accountant_id = $accountant->id;
                $note->note = $noteText;
                $note->status = 'active';
                if ($note->save()) {
                    $out = [
                        'status' => 'success',
                        'code' => 200,
                        'data' => CompanyNotesWidget::widget(['user' => $accountant, 'company' => $company]),
                    ];
                } else {
                    $errors = $note->getErrors();
                    $out = [
                        'success' => 'error',
                        'message' => 'Failed to save note: ' . implode('; ', array_map(function ($v) {
                            return implode(', ', $v);
                        }, $errors)),
                    ];
                }
            } else {
                $out = [
                    'success' => 'error',
                    'message' => 'Invalid input data.',
                    'input' => [
                        'token' => $token,
                        'companyId' => $companyId,
                        'noteText' => $noteText,
                    ],
                ];
            }
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = $out;
            return $response;
        } else {
            $this->renderLogout();
        }
    }

    public function actionListToCalendar()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $eventId = $request->post('id');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $rows = (new Query())
                ->select(['c.id', 'c.name', 'count(r.id) AS count'])
                ->from(['c' => 'company'])
                ->leftJoin(['r' => ReminderSchedule::tableName()], 'r.company_id = c.id AND r.type = \'calendar\' AND r.template_id = ' . intval($eventId))
                ->orderBy('c.name ASC')
                ->groupBy(['c.id', 'c.name']);

            $companies = $rows->all();
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'code' => 200,
                'data' => [
                    'list' => $companies
                ]
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionListToRegular()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $query = (new Query)
                ->select(['c.id', 'c.name', 'count(rrc.id) as count'])
                ->from(['c' => Company::tableName()])
                ->leftJoin(['rrc' => ReminderRegularCompany::tableName()], 'rrc.company_id = c.id and rrc.reminder_id = :id', [':id' => $id])
                ->groupBy(['c.id', 'c.name'])
                ->orderBy('c.name');

            $companies = $query->all();
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'code' => 200,
                'data' => [
                    'list' => $companies,
                    'query' => $query->createCommand()->getRawSql(),
                ]
            ];
        } else {
            return $this->renderLogout();
        }
    }

    public function actionUpdateListToRegular()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $reminderId = $request->post('reminder_id');
            $checkedCompanies = $request->post('checked_companies');
            $uncheckedCompanies = $request->post('uncheked_companies');
            // Удаляем существующие напоминания для этой компании и события
            if (!empty($uncheckedCompanies)) {
                ReminderRegularCompany::deleteAll([
                    'reminder_id' => $reminderId,
                    'company_id' => $uncheckedCompanies,
                ]);
                ReminderSchedule::deleteAll([
                    'type' => 'regular',
                    'template_id' => $reminderId,
                    'company_id' => $uncheckedCompanies,
                ]);
            }
            // Добавляем новые напоминания
            $errors = [];
            if (!empty($checkedCompanies)) {
                for ($i = 0; $i < count($checkedCompanies); $i++) {
                    $searchQuery = ReminderRegularCompany::find()
                        ->where([
                            'reminder_id' => $reminderId,
                            'company_id' => (int) $checkedCompanies[$i],
                        ]);
                    $existingReminder = $searchQuery->count();
                    if ($existingReminder > 0) {
                        continue;
                    }
                    $reminderCompany = new ReminderRegularCompany();
                    $reminderCompany->company_id = (int) $checkedCompanies[$i];
                    $reminderCompany->reminder_id = $reminderId;
                    if ($reminderCompany->save()) {
                        $reminderRegular = ReminderRegular::findOne(['id' => $reminderId]);
                        if ($reminderRegular) {
                            $companyId = (int) $checkedCompanies[$i];
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
                            $deadlineDate = date('Y-m-') . str_pad($reminderRegular->deadline_day, 2, '0', STR_PAD_LEFT);
                            $escalationDate = CalendarService::getClosestWorkingDay(date('Y-m-d', strtotime($deadlineDate . ' -1 day')));
                            $reminder_2_date = CalendarService::getClosestWorkingDay(date('Y-m-d', strtotime($escalationDate . ' -1 day')));
                            $reminder_1_date = CalendarService::getClosestWorkingDay(date('Y-m-d', strtotime($reminder_2_date . ' -1 day')));
                            $reminderSchedule = new ReminderSchedule();
                            $reminderSchedule->company_id = (int) $checkedCompanies[$i];
                            $reminderSchedule->type = 'regular';
                            $reminderSchedule->template_id = $reminderId;
                            $reminderSchedule->updated_at = date('Y-m-d H:i:s');
                            $reminderSchedule->deadline_date = $deadlineDate;
                            $reminderSchedule->reminder_1_date = $reminder_1_date;
                            $reminderSchedule->reminder_2_date = $reminder_2_date;
                            $reminderSchedule->escalation_date = $escalationDate;
                            $reminderSchedule->target_month = date('Y-m-01', time());
                            $reminderSchedule->status = ReminderSchedule::STATUS_PENDING;
                            $reminderSchedule->message = ($lang['lang'] == 'ru') ? $reminderRegular->text_ru : $reminderRegular->text_rs;
                            $reminderSchedule->save();
                            if ($reminderSchedule->hasErrors()) {
                                $errors[] = $reminderSchedule->getErrors();
                            }
                        }
                    }
                    if ($reminderCompany->hasErrors()) {
                        $errors[] = $reminderCompany->getErrors();
                    }
                }
            }
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Reminders updated successfully.',
            ];
            if ($errors) {
                $response->data['errors'] = $errors;
            }
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionUpdateCalendarReminders()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $reminderId = $request->post('reminder_id');
            $checkedCompanies = $request->post('checked_companies');
            $uncheckedCompanies = $request->post('uncheked_companies');
            // Удаляем существующие напоминания для этой компании и события
            if (!empty($uncheckedCompanies)) {
                ReminderSchedule::deleteAll([
                    'type' => 'calendar',
                    'template_id' => $reminderId,
                    'company_id' => $uncheckedCompanies,
                ]);
            }
            // Добавляем новые напоминания
            $n = 0;
            $errors = [];
            if (!empty($checkedCompanies)) {
                for ($i = 0; $i < count($checkedCompanies); $i++) {
                    $companyId = (int) $checkedCompanies[$i];
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
                    $ps = TaxCalendar::findOne(['id' => $reminderId]);
                    $searchQuery = ReminderSchedule::find()
                        ->where([
                            'type' => 'calendar',
                            'template_id' => $reminderId,
                            'company_id' => (int) $checkedCompanies[$i],
                        ]);
                    $existingReminder = $searchQuery->count();
                    if ($existingReminder > 0) {
                        continue;
                    }
                    $reminder = new ReminderSchedule();
                    $reminder->company_id = (int) $checkedCompanies[$i];
                    $reminder->type = 'calendar';
                    $reminder->template_id = $reminderId;
                    // $reminder->created_at = date('Y-m-d H:i:s');
                    $reminder->updated_at = date('Y-m-d H:i:s');
                    $reminder->deadline_date = date('Y-m-d H:i:s', strtotime($ps->input_date));
                    $reminder->reminder_1_date = date('Y-m-d H:i:s', strtotime($ps->reminder_1_date));
                    $reminder->reminder_2_date = date('Y-m-d H:i:s', strtotime($ps->reminder_2_date));
                    $reminder->escalation_date = date('Y-m-d H:i:s', strtotime($ps->escalation_date));
                    $reminder->target_month = date('Y-m-01', strtotime($ps->target_month));
                    $reminder->message = ($lang['lang'] == 'ru') ? $ps->activity_text_ru : $ps->activity_text_rs;
                    $r = $reminder->save();
                    if (!$r) {
                        $errors[] = $reminder->getErrors();
                    } else {
                        $n++;
                    }
                }
            }
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Reminders updated successfully.',
                'n' => $n,
                'errors' => $errors,
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionDeleteCalendarReminder()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $reminderId = $request->post('id');
            Reminder::deleteAll(['type' => 'calendar', 'template_id' => $reminderId]);
            TaxCalendar::deleteAll(['id' => $reminderId]);
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Calendar reminder deleted successfully.',
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionUpdateReminderDetails()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $reminderId = $request->post('id');
            $text = $request->post('text');
            $tc = TaxCalendar::findOne(['id' => $reminderId]);
            if ($tc) {
                if ($accountant->lang == 'ru') {
                    $tc->activity_text_ru = $text;
                    $data = [
                        'text' => $text,
                        'from' => 'ru',
                        'to' => 'rs',
                    ];
                    $tc->activity_text_rs = $this->makeN8nWebhookCall('translate', $data)['data']['translation'] ?? '';
                } else {
                    $tc->activity_text_rs = $text;
                    $data = [
                        'text' => $text,
                        'from' => 'rs',
                        'to' => 'ru',
                    ];
                    $tc->activity_text_ru = $this->makeN8nWebhookCall('translate', $data)['data']['translation'] ?? '';
                }
                $tc->save();
            }
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Reminder details updated successfully.',
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionIndex()
    {
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = ['method' => __METHOD__];
        return $response;
        // return $this->render('index');
    }
}
