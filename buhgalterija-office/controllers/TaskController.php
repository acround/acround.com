<?php

namespace app\controllers;

use yii\db\Query;
use yii\web\Response;
use app\controllers\BaseController;
use app\components\TaskListWidget;
use app\components\TaskViewDocumentListWidget;
use app\models\Accountant;
use app\models\Company;
use app\models\Task;
use app\models\TaskComment;
use app\services\AuthService;
use yii\db\Expression;

class TaskController extends BaseController
{

    public function getDataForPage($accountant, $status = null)
    {
        $taskQuery = Task::find();
        if ($accountant->rule !== Accountant::RULE_CEO) {
            $taskQuery->where(['accountant_id' => $accountant->id]);
        }
        if ($status !== null) {
            if ($status == Task::STATUS_OVERDUE) {
                $taskQuery->andWhere(['status' => Task::STATUS_OVERDUE]);
            } else {
                $taskQuery->andWhere(['status' => $status]);
            }
        }
        $tasks = $taskQuery->all();

        $filterStatusQuery = (new Query())
            ->select('status')
            ->from(Task::tableName())
            ->distinct()
            ->from('task')
            ->orderBy('status');

        $filterPriorityQuery = (new Query())
            ->select('priority')
            ->from(Task::tableName())
            ->distinct()
            ->from('task')
            ->orderBy('priority');

        $filterCompanyQuery = (new Query())
            ->select('c.*')
            ->from(['c' => Company::tableName()])
            ->distinct()
            ->innerJoin(['t' => Task::tableName()], 't.company_id = c.id');
        if ($accountant->rule !== Accountant::RULE_CEO) {
            $filterCompanyQuery->where(['t.accountant_id' => $accountant->id]);
        }

        if (AuthService::hasPermission($accountant, 'viewAccountants')) {
            $filterAssignedToQuery = (new Query())
                ->select('a.*')
                ->from(['a' => Accountant::tableName()])
                ->distinct()
                ->innerJoin(['t' => Task::tableName()], 't.accountant_id = a.id');
            $filterAssignedTo = $filterAssignedToQuery->all();
        } else {
            $filterAssignedTo = [];
        }

        $data = [
            'user' => $accountant,
            'tasks' => $tasks,
            'filterStatus' => $filterStatusQuery->all(),
            'filterPriority' => $filterPriorityQuery->all(),
            'filterCompany' => $filterCompanyQuery->all(),
            'filterAssignedTo' => $filterAssignedTo,
        ];
        $data['back'] = $status !== null;
        return $data;
    }

    public function actionPage($status = null)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $data = $this->getDataForPage($accountant, $status);
            return $this->renderPage($data);
        } else {
            return $this->renderLogout([$accountant, $accountant->isValid()]);
        }
    }

    public function actionFilter()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $name = $request->post('name');
            $status = $request->post('status');
            $priority = $request->post('priority');
            $company = $request->post('company');
            $assignedTo = $request->post('assignedTo');
            $taskQuery = Task::find();
            if ($accountant->rule !== Accountant::RULE_CEO) {
                $taskQuery->where(['accountant_id' => $accountant->id]);
            }
            if ($name) {
                $taskQuery
                    ->andWhere([
                        'or',
                        ['ilike', 'category', $name],
                        ['ilike', 'request', $name],
                    ]);
            }
            if ($status) {
                $taskQuery->andWhere(['status' => $status]);
            }
            if ($priority) {
                $taskQuery->andWhere(['priority' => $priority]);
            }
            if ($company) {
                $taskQuery->andWhere(['company_id' => $company]);
            }
            if ($assignedTo) {
                $taskQuery->andWhere(['accountant_id' => $assignedTo]);
            }
            $tasks = $taskQuery->all();
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'data' => TaskListWidget::widget(['user' => $accountant, 'tasks' => $tasks, 'company' => null]),
            ];
            return $response;
        } else {
            return $this->renderLogout($accountant);
        }
    }

    public function actionView()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $data = [
                'user' => $accountant,
                'task' => Task::findOne(['id' => $id]),
            ];
            return $this->renderPage($data, 'view');
        } else {
            return $this->renderLogout($accountant);
        }
    }

    public function actionEdit()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $task = $id ? Task::findOne(['id' => $id]) : (new Task());
            $data = [
                'user' => $accountant,
                'task' => $task,
                'companies' => Company::find()->orderBy('name')->all(),
                'accountants' => Accountant::find()->select(['id', 'lastname', 'firstname'])->where(['>', 'id', new Expression(0)])->orderBy(['lastname' => SORT_ASC, 'firstname' => SORT_ASC])->all(),
                'statuses' => Task::getStatuses(),
                'priorities' => Task::getPriorities(),
            ];
            return $this->renderPage($data, 'edit');
        } else {
            return $this->renderLogout($accountant);
        }
    }

    public function actionDocuments()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $task = $id ? Task::findOne(['id' => $id]) : (new Task());
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            if ($task) {
                $data = [
                    'user' => $accountant,
                    'documents' => $task->getDocuments(),
                ];
                $response->data = [
                    'status' => 'success',
                    'data' => TaskViewDocumentListWidget::widget($data),
                ];
            } else {
                $response->data = [
                    'status' => 'error',
                    'message' => 'Задача не найдена',
                ];
            }
            return $response;
        } else {
            return $this->renderLogout($accountant);
        }
    }

    public function actionSave()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $task = $id ? Task::findOne(['id' => $id]) : (new Task());
            $task->category = $request->post('category');
            $task->request = $request->post('request');
            $task->status = $request->post('status');
            $task->priority = $request->post('priority');
            $task->due_date = $request->post('due_date');
            $task->company_id = $request->post('company_id');
            $task->accountant_id = $request->post('accountant_id');
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            if ($task->save()) {
                $id = $task->id;
                $response->data = [
                    'status' => 'success',
                    'id' => $task->id,
                    'task' => Task::findOne(['id' => $id]),
                ];
            } else {
                $response->data = [
                    'status' => 'error',
                    'message' => $task->getErrors(),
                    'task' => $task,
                ];
            }
            return $response;
        } else {
            return $this->renderLogout($accountant);
        }
    }

    public function actionComment()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $commentText = $request->post('comment_text');
            $comment = new TaskComment();
            $comment->task_id = $id;
            $comment->accountant_id = $accountant->id;
            $comment->text = $commentText;
            $comment->save();
            $data = [
                'user' => $accountant,
                'task' => Task::findOne(['id' => $id]),
            ];
            return $this->renderPage($data, 'addComment');
        } else {
            return $this->renderLogout($accountant);
        }
    }

    public function actionFinish()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $task = Task::findOne(['id' => $id]);
            $task->status = Task::STATUS_DONE;
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            if ($task->save()) {
                $response->data = [
                    'status' => 'success',
                    'task' => Task::findOne(['id' => $id]),
                ];
            } else {
                $response->data = [
                    'status' => 'error',
                    // 'message' => implode("\n", $task->getErrors()),
                    'message' => $task->getErrors(),
                    'task' => $task,
                ];
            }
            return $response;
        } else {
            return $this->renderLogout($accountant);
        }
    }
}
