<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\db\Query;
use yii\web\ErrorAction;
use app\services\AuthService;
use app\models\Accountant;
use app\models\Company;
use app\models\Task;
use app\models\Document;
use app\models\TaskActivity;
use app\models\TaskDocument;
use app\services\SvgService;

class SiteController extends BaseController
{

    public $user;

    public function mainMenu()
    {
        $mainMenu = [
            'dashboard' => [
                'url' => '/site/page',
                'picture' => SvgService::svg('main'),
                'active' => true,
                'rules' => ['accountant', 'admin', 'ceo',],
            ],
            'companies' => [
                'url' => '/company/page',
                'picture' => SvgService::svg('activity'),
                'active' => true,
                'rules' => ['accountant', 'admin', 'ceo',],
            ],
            'tasks' => [
                'url' => '/task/page',
                'picture' => SvgService::svg('tasks'),
                'active' => true,
                'rules' => ['accountant', 'admin', 'ceo',],
            ],
            'documents' => [
                'url' => '/document/page',
                'picture' => SvgService::svg('document'),
                'active' => true,
                'rules' => ['accountant', 'admin', 'ceo',],
            ],
            'reminders' => [
                'url' => '/reminder/page',
                'picture' => SvgService::svg('reminder'),
                'active' => true,
                'rules' => ['accountant', 'admin', 'ceo',],
            ],
            'settings' => [
                'url' => '/settings/page',
                'picture' => SvgService::svg('settings'),
                'active' => false,
                'rules' => ['accountant', 'admin', 'ceo',],
            ],
            'accountants' => [
                'url' => '/accountant/list',
                'picture' => SvgService::svg('settings'),
                'active' => true,
                'rules' => ['admin', 'ceo'],
            ],
        ];
        return $mainMenu;
    }

    protected function putHtml($label = '')
    {
        $filePath = $filePath = \Yii::getAlias('@app/web/index.html');
        if (!is_file($filePath)) {
            throw new NotFoundHttpException('Frontend index.html file not found. Have you built the React application and placed it in ' . $filePath . '?');
        }
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'inline');
        $response->data = $label . file_get_contents($filePath);
        return $response;
    }


    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $this->layout = 'main';
        $cookies = Yii::$app->request->cookies;

        if (($cookie = $cookies->get('token')) !== null) {
            $token = $cookie->value;
        } else {
            $token = 'token_not_found';
        }

        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        Yii::$app->view->params['accountant'] = $accountant;
        Yii::$app->view->params['token'] = $token;
        $data = [
            'user' => $accountant,
        ];
        return $this->render('empty', $data);
    }

    protected function getDataForPage($accountant)
    {
        $permissions = AuthService::getPermissions($accountant);
        $viewAccountants = array_key_exists('viewAccountants', $permissions);
        $overdueTasksQuery = (new Query())
            ->select(['a.id', 'COUNT(t.id) AS "overdue_tasks"'])
            ->from(['a' => 'accountant'])
            ->leftJoin(['t' => Task::tableName()], 't.accountant_id = a.id')
            ->where(['!=', 'a.rule', 'bot'])
            ->andWhere(['!=', 'a.rule', 'admin'])
            ->andWhere('t.status not in (\'done\', \'closed\', \'archived\')')
            ->andWhere('t.due_date < CURRENT_DATE')
            ->andWhere(['!=', 'a.rule', 'ceo'])
            ->groupBy('a.id')
            ->orderBy(['a.id' => SORT_ASC]);
        if ($viewAccountants) {
            $accountantQuery = (new Query())
                ->select(['c.id', 'c.firstname', 'c.lastname', 'c.rule', 'c.lang', 'c.email', 'COUNT(t.id) AS tasks'])
                ->from(['c' => Accountant::tableName()])
                ->leftJoin(['t' => Task::tableName()], 't.accountant_id = c.id')
                ->where(['!=', 'c.rule', 'bot'])
                ->andWhere(['!=', 'c.rule', 'admin'])
                ->andWhere(['!=', 'c.rule', 'ceo'])
                ->groupBy(['c.id', 'c.firstname', 'c.lastname', 'c.rule', 'c.lang', 'c.email'])
                ->orderBy(['tasks' => SORT_DESC, 'c.lastname' => SORT_ASC, 'c.firstname' => SORT_ASC]);
            $accountantsRaw = $accountantQuery->all();
            $accountants = [];
            foreach ($accountantsRaw as $accountantOne) {
                $accountants[$accountantOne['id']] = $accountantOne;
            }

            if ($accountant->rule !== 'ceo') {
                $overdueTasksQuery->andWhere('t.accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
            }
            $recentActivity = [];
        } else {
            $accountants = [];
            $recentActivity = TaskActivity::find()
                ->where('accountant_id = :accountant_id', ['accountant_id' => $accountant->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->limit(10)
                ->all();
        }
        $overdueTasks = $overdueTasksQuery->all();
        foreach ($overdueTasks as $overdueTask) {
            $accountants[$overdueTask['id']]['overdueTasks'] = $overdueTask['overdue_tasks'];
        }
        $upcomingDeadlinesQuery = (new Query())
            ->select([
                't.id as task_id',
                'c.id as company_id',
                'c.name as company_name',
                'a.id as accountant_id',
                '(a.firstname || \' \' || a.lastname) as accountant_name',
                't.category',
                't.request',
                't.status',
                't.due_date',
                't.priority',
                'DATE_PART(\'day\', due_date - CURRENT_DATE) AS time_left'

            ])
            ->from(['t' => 'task'])
            ->rightJoin(['c' => 'company'], 'c.id=t.company_id')
            ->leftJoin(['a' => 'accountant'], 'a.id = t.accountant_id')
            ->andWhere('t.status not in (\'done\', \'closed\', \'archived\')')
            ->orderBy(['due_date' => SORT_ASC])
            ->limit(5);
        if ($accountant->rule !== 'ceo') {
            $upcomingDeadlinesQuery->andWhere('t.accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
        }
        $upcomingDeadlines = $upcomingDeadlinesQuery->all();

        // $customers = Customer::find()->count();
        $companies = Company::find()->count();
        $activeTasksQuery = Task::find()->where(['status' => 'inProgress']);
        if ($accountant->rule !== 'ceo') {
            $activeTasksQuery->andWhere('accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
        }
        $activeTasks = $activeTasksQuery->count();

        $overdueTasksQuery = Task::find()
            ->where(['<', 'due_date', date('Y-m-d')])
            ->andWhere(['!=', 'status', Task::STATUS_DONE]);
        if ($accountant->rule !== 'ceo') {
            $overdueTasksQuery->andWhere('accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
        }
        $docsToCheckQuery = Document::find()
            ->from(['d' => Document::tableName()])
            ->where(['d.status' => 'uploaded']);
        if ($accountant->rule !== 'ceo') {
            $docsToCheckQuery
                ->leftJoin(['td' => TaskDocument::tableName()], 'td.document_id = d.id')
                ->leftJoin(['t' => Task::tableName()], 't.id = td.task_id')
                ->andWhere('t.accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
        }
        $docsToCheck = $docsToCheckQuery->count();
        $data = [
            'user' => $accountant,
            'data' => [
                'accountants' => $accountants,
                'clents' => $companies,
                'activeTasks' => $activeTasks,
                'overdueTasks' => $overdueTasks,
                'upcomingDeadlines' => $upcomingDeadlines,
                'docsToCheck' => $docsToCheck,
                'activities' => $recentActivity,
                'viewAccountants' => $viewAccountants,
            ],
        ];
        return $data;
    }

    public function actionLoad()
    {
        $this->layout = false;
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if (!$accountant->isValid()) {
            $data = $this->render('login');
        } else {
            $dataForRendering = $this->getDataForPage($accountant);
            $dataForRendering['menu'] = $this->mainMenu();
            $data = $this->render('index', $dataForRendering);
        }
        $response->data = [
            'status' => 'success',
            'code' => 200,
            'data' => $data,
        ];
        return $response;
    }

    public function actionPage()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $data = $this->getDataForPage($accountant);
            return $this->renderPage($data);
        } else {
            return $this->renderLogout();
        }
    }

    public function actionError()
    {
        $ex = Yii::$app->getErrorHandler()->exception;
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = ['status' => 'error', 'message' => $ex->getMessage(), 'exception' => get_class($ex)];
        return $response;
    }
}
