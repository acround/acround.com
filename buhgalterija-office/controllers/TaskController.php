<?php

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\web\Response;
use app\controllers\BaseController;
use app\widgets\TaskListWidget;
use app\widgets\TaskViewDocumentListWidget;
use app\models\Accountant;
use app\models\Company;
use app\models\Document;
use app\models\Task;
use app\models\TaskComment;
use app\services\AuthService;
use yii\db\Expression;

class TaskController extends BaseController
{

    public function getDataForPage($accountant, $filters = [], $back)
    {
        $taskQuery = Task::find();
        if ($accountant->rule !== Accountant::RULE_CEO) {
            $taskQuery->where(['accountant_id' => $accountant->id])
                ->orderBy(['status' => SORT_ASC, 'id' => SORT_ASC]);
        }
        if (isset($filters['status']) && $filters['status']) {
            $taskQuery->andWhere(['status' => $filters['status']]);
        } else {
            $taskQuery->andWhere(['status' => Task::getStatusesInProgress()]);
        }
        if (isset($filters['priority'])) {
            $taskQuery->andWhere(['priority' => $filters['priority']]);
        }
        if (isset($filters['assignedTo'])) {
            $taskQuery->andWhere(['accountant_id' => $filters['assignedTo']]);
        }

        if (isset($filters['name'])) {
            $taskQuery
                ->andWhere([
                    'or',
                    ['ilike', 'category', $filters['name']],
                    ['ilike', 'request', $filters['name']],
                ]);
        }
        if ($filters['company']) {
            $taskQuery->andWhere(['company_id' => $filters['company']]);
        }

        $sorting = $filters['sorting'] ?? [];
        $sortArray = [];
        if (!empty($sorting)) {
            foreach ($sorting as $sort) {
                $sortValue = $sort['value'] == 'asc' ? SORT_ASC : SORT_DESC;
                $sortArray[$sort['field']] = $sortValue;
            }
            $taskQuery->orderBy($sortArray);
        }

        $totalTasks = $taskQuery->count();
        $tasks = $taskQuery->limit(self::PAGE_LENGTH)->offset($filters['offset'])->all();

        $filterStatusQuery = (new Query())
            ->select('status')
            ->from(Task::tableName())
            ->distinct()
            ->from('task')
            ->orderBy('status');
        if ($accountant->rule !== Accountant::RULE_CEO) {
            $filterStatusQuery->where(['accountant_id' => $accountant->id]);
        }
        $filterStatusRaw = [];
        foreach ($filterStatusQuery->all() as $item) {
            $filterStatusRaw[] = $item['status'];
        };
        $filterStatus = [];
        $filterStatus[] = '-';
        foreach (Task::getStatusesInProgress() as $status) {
            if (in_array($status, $filterStatusRaw)) {
                $filterStatus[] = $status;
            }
        }
        $filterStatus[] = '-';
        foreach (Task::getStatusesCompleted() as $status) {
            if (in_array($status, $filterStatusRaw)) {
                $filterStatus[] = $status;
            }
        }

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
            'total' => $totalTasks,
            'name' => $filters['name'] ?? '',
            'sorting' => $filters['sorting'] ?? [],
            'page' => $filters['page'] ?? 1,
            'status' => $filters['status'] ?? '',
            'company' => $filters['company'] ?? '',
            'priority' => $filters['priority'] ?? '',
            'assignedTo' => $filters['assignedTo'] ?? '',
            'limit' => self::PAGE_LENGTH,
            'filterStatus' => $filterStatus,
            'filterPriority' => $filterPriorityQuery->all(),
            'filterCompany' => $filterCompanyQuery->all(),
            'filterAssignedTo' => $filterAssignedTo,
            'back' => $back,
        ];
        return $data;
    }

    public function actionPage($status = null)
    {
        $this->layout = false;
        $request = Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $back = $status !== null;
            $page = $request->post('page') ?? 1;
            $offset = ($page - 1) * self::PAGE_LENGTH;
            $name = $request->post('name');
            $status = $status ?? $request->post('status');
            $sorting = $request->post('sorting') ?? [];
            if (!$status) {
                $status = Task::getStatusesInProgress();
            }
            $priority = $request->post('priority');
            $company = $request->post('company');
            $assignedTo = $request->post('assignedTo');
            $filters = [
                'name' => $name,
                'status' => $status,
                'priority' => $priority,
                'company' => $company,
                'assignedTo' => $assignedTo,
                'sorting' => $sorting,
                'offset' => $offset,
                'page' => $page,
            ];
            $data = $this->getDataForPage($accountant, $filters, $back);
            return $this->renderPage($data);
        } else {
            return $this->renderLogout([$accountant, $accountant->isValid()]);
        }
    }

    public function actionFilter()
    {
        $this->layout = false;
        $request = Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $page = $request->post('page') ?? 1;
            $offset = ($page - 1) * self::PAGE_LENGTH;
            $name = $request->post('name');
            $status = $request->post('status');
            if (!$status) {
                $status = Task::getStatusesInProgress();
            }
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
            $sorting = $request->post('sorting') ?? [];
            $sortArray = [];
            if (!empty($sorting)) {
                foreach ($sorting as $sort) {
                    $sortValue = $sort['value'] == 'asc' ? SORT_ASC : SORT_DESC;
                    $sortArray[$sort['field']] = $sortValue;
                }
                $taskQuery->orderBy($sortArray);
            }
            $tasksTotal = $taskQuery->count();
            $taskQuery
                ->limit(self::PAGE_LENGTH)
                ->offset($offset);
            $tasks = $taskQuery->all();
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'data' => TaskListWidget::widget([
                    'user' => $accountant,
                    'tasks' => $tasks,
                    'company' => null,
                    'sorting' => $sorting,
                    'total' => $tasksTotal,
                    'page' => $page,
                    'limit' => self::PAGE_LENGTH
                ]),
                'count' => $tasksTotal,
            ];
            return $response;
        } else {
            return $this->renderLogout($accountant);
        }
    }

    public function actionView()
    {
        $this->layout = false;
        $request = Yii::$app->request;
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
        $request = Yii::$app->request;
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
        $request = Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $task = $id ? Task::findOne(['id' => $id]) : (new Task());
            $response = Yii::$app->response;
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
        $request = Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $task = $id ? Task::findOne(['id' => $id]) : (new Task());
            $oldStatus = $task->status;
            $task->category = $request->post('category');
            $task->request = $request->post('request');
            $task->status = $request->post('status');
            $task->priority = $request->post('priority');
            $task->due_date = $request->post('due_date');
            $task->company_id = $request->post('company_id');
            $task->accountant_id = $request->post('accountant_id');
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            if ($task->save()) {
                if ($task->status == Task::STATUS_ARCHIVED) {
                    $documents = $task->getDocuments();
                    foreach ($documents as $document) {
                        $document->status = Document::STATUS_ARCHIVED;
                        $document->save();
                    }
                } elseif (($oldStatus == Task::STATUS_ARCHIVED) && ($task->status != Task::STATUS_ARCHIVED)) {
                    $documents = $task->getDocuments();
                    foreach ($documents as $document) {
                        $command = Yii::$app->db
                            ->createCommand()
                            ->update(Document::tableName(), ['status' => Document::STATUS_CHECKED], 'id = ' . $document['id']);
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            $command->execute();
                            $transaction->commit();
                        } catch (\Exception $e) {
                            $transaction->rollBack();
                        }
                    }
                }
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
        $request = Yii::$app->request;
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
        $request = Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $task = Task::findOne(['id' => $id]);
            $task->status = Task::STATUS_DONE;
            $response = Yii::$app->response;
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
                    'message' => $task->getErrors(),
                    'task' => $task,
                ];
            }
            return $response;
        } else {
            return $this->renderLogout($accountant);
        }
    }

    public function actionArchive()
    {
        $this->layout = false;
        $request = Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $task = Task::findOne(['id' => $id]);
            $task->status = Task::STATUS_ARCHIVED;
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $saving = $task->save();
            if ($saving) {
                $documents = $task->getDocuments();
                foreach ($documents as $document) {
                    $command = Yii::$app->db
                        ->createCommand()
                        ->update(Document::tableName(), ['status' => Document::STATUS_ARCHIVED], 'id = ' . $document['id']);
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $command->execute();
                        $transaction->commit();
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
                $response->data = [
                    'status' => 'success',
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

    public function actionSuggest()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $query = $request->post('query');
            $taskQuery = (new Query())
                ->select(['d.*'])
                ->distinct()
                ->from(['d' => Task::tableName()])
                ->where([
                    'or',
                    ['ilike', 'category', $query],
                    ['ilike', 'request', $query],
                ])
                ->limit(self::SUGGESTS_COUNT);
            $data = $taskQuery->all();
            $data = array_map(function ($item) {
                $item['name'] = $item['category'];
                return [
                    'id' => $item['id'],
                    'name' => mb_strlen($item['name'], 'utf-8') > 40 ? mb_substr($item['name'], 0, 40, 'utf-8') . '…' : $item['name'],
                ];
            }, $data);
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data =
                [
                    'status' => 'success',
                    'data' => $data,
                ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }
}
