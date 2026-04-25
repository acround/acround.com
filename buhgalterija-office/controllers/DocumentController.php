<?php

namespace app\controllers;

use yii\web\Response;
use yii\db\Query;
use app\widgets\DocViewActivityWidget;
use app\widgets\DocListWidget;
use app\models\Accountant;
use app\models\Company;
use app\models\Document;
use app\models\DocumentComment;
use app\models\DocumentType;
use app\models\Task;
use app\models\TaskDocument;
use Exception;

class DocumentController extends BaseController
{

    protected function getDocumentQuery($accountant, $filters)
    {
        $docsQuery = Document::find()
            ->select(['d.*'])
            ->distinct()
            ->from(['d' => Document::tableName()]);
        if ($accountant->rule !== 'ceo') {
            $docsQuery
                ->leftJoin(['td' => TaskDocument::tableName()], 'd.id = td.document_id')
                ->leftJoin(['t' => Task::tableName()], 'td.task_id = t.id')
                ->leftJoin(['a' => Accountant::tableName()], 't.accountant_id = a.id')
                ->where('t.accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
        }
        if ($filters['name']) {
            $docsQuery->andWhere([
                'OR',
                ['ilike', 'd.filename', $filters['name']],
                ['ilike', 'd.ocr_text', $filters['name']],
                ['ilike', 'd.summary', $filters['name']],
                ['ilike', 'd.category', $filters['name']],
            ]);
        }
        if ($filters['status']) {
            $docsQuery->andWhere(['d.status' => $filters['status']]);
        }
        if ($filters['priority']) {
            $docsQuery->andWhere(['d.priority' => $filters['priority']]);
        }
        if ($filters['company']) {
            $docsQuery->andWhere(['d.company_id' => $filters['company']]);
        }
        if ($filters['type']) {
            $docsQuery->andWhere(['d.type_id' => $filters['type']]);
        }
        return $docsQuery;
    }

    public function getDataForPage($accountant, $filters = [], $back = false)
    {
        $docsQuery = $this->getDocumentQuery($accountant, $filters);
        $total = $docsQuery->count();
        $page = $filters['page'] ?? 1;
        $offset = ($page - 1) * self::PAGE_LENGTH;
        $docsQuery->offset($offset)->limit(self::PAGE_LENGTH);
        $docs = $docsQuery->all();

        $filterCompanyQuery = (new Query())
            ->select(['c.id', 'c.name'])
            ->distinct()
            ->from(['d' => Document::tableName()])
            ->innerJoin(['c' => Company::tableName()], 'c.id = d.company_id');
        if ($accountant->rule !== 'ceo') {
            $filterCompanyQuery
                ->leftJoin(['td' => TaskDocument::tableName()], 'd.id = td.document_id')
                ->leftJoin(['t' => Task::tableName()], 'td.task_id = t.id')
                ->leftJoin(['a' => Accountant::tableName()], 't.accountant_id = a.id')
                ->where('t.accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
        }

        $filterDocumentTypeQuery = (new Query())
            ->select(['t.id', 't.name'])
            ->distinct()
            ->from(['d' => Document::tableName()])
            ->innerJoin(['t' => DocumentType::tableName()], 't.id = d.type_id')
            ->orderBy('t.name ASC');

        $filterStatusQuery = (new Query())
            ->select('status')
            ->distinct()
            ->from(Document::tableName());

        $data = [
            'user' => $accountant,
            'documents' => $docs,
            'total' => $total,
            'limit' => self::PAGE_LENGTH,
            'filters' => $filters,
            'filterCompany' => $filterCompanyQuery->all(),
            'filterDocumentType' => $filterDocumentTypeQuery->all(),
            'filterStatus' => $filterStatusQuery->all(),
            'back' => $back,
        ];
        return $data;
    }

    public function actionPage($status = null)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $back = $status !== null;
            $page = $request->post('page') ?? 1;
            $offset = ($page - 1) * self::PAGE_LENGTH;
            $filters = [
                'name' => $request->post('name'),
                'status' => $status ?? $request->post('status'),
                'priority' => $request->post('priority'),
                'company' => $request->post('company'),
                'type' => $request->post('type'),
                'page' => $page,
                'offset' => $offset,
            ];
            $data = $this->getDataForPage($accountant, $filters, $back);
            return $this->renderPage($data);
        } else {
            return $this->renderLogout();
        }
    }

    public function actionView()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $docId = $request->post('id');
            $doc = Document::findOne(['id' => $docId]);
            $data = [
                'user' => $accountant,
                'document' => $doc,
            ];
            return $this->renderPage($data, 'view');
        } else {
            return $this->renderLogout();
        }
    }


    public function actionFilter()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $page = $request->post('page') ?? 1;
            $offset = ($page - 1) * self::PAGE_LENGTH;
            $filters = [
                'name' => $request->post('name'),
                'status' => $request->post('status'),
                'priority' => $request->post('priority'),
                'company' => $request->post('company'),
                'type' => $request->post('type'),
                'page' => $page,
                'offset' => $offset,
            ];
            $docsQuery = $this->getDocumentQuery($accountant, $filters);
            $total = $docsQuery->count();
            $filters['total'] = $total;
            $page = $filters['page'] ?? 1;
            $offset = ($page - 1) * self::PAGE_LENGTH;
            $docsQuery->offset($offset)->limit(self::PAGE_LENGTH);
            $docs = $docsQuery->all();
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'data' => DocListWidget::widget(['user' => $accountant, 'documents' => $docs, 'company' => null, 'filters' => $filters, 'total' => $total, 'limit' => self::PAGE_LENGTH]),
                'count' => $total,
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionUpload()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $viewer = Accountant::findIdentityByAccessToken($token);
        if ($viewer->isValid()) {
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $id = $request->post('task_id');
            $file = $_FILES['document'] ?? null;
            if ($id && $file && ($file['error'] == UPLOAD_ERR_OK)) {
                try {
                    $task = Task::findOne(['id' => $id]);
                    $errors = [];
                    $document = new Document();
                    $document->filename = $file['name'];
                    $document->mimetype = $file['type'];
                    $document->content = file_get_contents($file['tmp_name']);
                    $document->status = Document::STATUS_UPLOADED;
                    $document->company_id = $task->company_id ?? null;
                    $metadata = [
                        'chatId' => '',
                        'fileId' => '',
                        'fileName' => $file['name'],
                        'mimeType' => $file['type'],
                        'userName' => $viewer->firstname . ' ' . $viewer->lastname,
                    ];
                    $document->metadata = $metadata;
                    try {
                        if (!$document->save()) {
                            $errors[] = $document->getErrors();
                        }
                    } catch (\Exception $e) {
                        $errors[] = [
                            'doc' => $document->getAttributes(null, ['metadata', 'content',]),
                            'message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTraceAsString(),
                        ];
                    }
                    if ($task && $document->id) {
                        $taskDocument = new TaskDocument();
                        $taskDocument->task_id = $id;
                        $taskDocument->document_id = $document->id;
                        if (!$taskDocument->save()) {
                            $errors[] = $taskDocument->getErrors();
                        }
                    }
                    if ($errors) {
                        $response->data = [
                            'status' => 'error',
                            'message' => implode(',\n', $errors),
                        ];
                    } else {
                        $response->data = [
                            'status' => 'success',
                            'data' => [
                                'doc' => $document->id,
                            ],
                        ];
                    }
                } catch (Exception $e) {
                    $response->data = [
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ];
                }
            } else {
                $response->data = [
                    'status' => 'error',
                    'user' => $viewer,
                    'task_id' => $id,
                    'post' => $_POST,
                    'files' => $_FILES,
                ];
            }
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionFile($id)
    {
        $this->layout = false;
        $response = \Yii::$app->response;
        $document = Document::findOne(['id' => $id]);
        if ($document) {
            $response->format = Response::FORMAT_RAW;
            $response->headers->add('Content-Type', $document->mimetype);
            $response->headers->add('Content-Disposition', "inline; filename=\"{$document->filename}\"");
            $response->content = stream_get_contents($document->content);
            return $response;
        } else {
            throw new \yii\web\NotFoundHttpException('Document not found');
        }
    }

    public function actionImage($id)
    {
        $this->layout = false;
        // $response = \Yii::$app->response;
        $document = Document::findOne(['id' => $id]);
        $this->layout = false;
        return $this->render('image', [
            'document' => $document,
        ]);
    }

    public function actionChangeStatus()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $response = \Yii::$app->response;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $docId = $request->post('id');
            $newStatus = $request->post('status');
            $newStatus = Document::$steps[$newStatus] ?? $newStatus;
            $document = Document::findOne(['id' => $docId]);
            if ($document) {
                $document->status = $newStatus;
                $query = new Query();
                $res = $query->createCommand()->update(Document::tableName(), ['status' => $newStatus], ['id' => $docId])->execute();
                $response->format = Response::FORMAT_JSON;
                if ($res) {
                    $document->addActivity($accountant->id, $newStatus);
                    $response->data =
                        [
                            'status' => 'success',
                            'data' => $this->renderPartial('_doc_status_block', [
                                'user' => $accountant,
                                'document' => $document,
                            ]),
                            'activity' => DocViewActivityWidget::widget([
                                'user' => $accountant,
                                'document' => $document,
                            ]),
                            'errors' => $document->getErrors(),
                        ];
                    return $response;
                } else {
                    $response->data =
                        [
                            'status' => 'error',
                            'errors' => $document->getErrors(),
                        ];
                    return $response;
                }
            } else {
                throw new \yii\web\NotFoundHttpException('Document not found');
            }
        } else {
            return $this->renderLogout();
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
            $comment = new DocumentComment();
            $comment->document_id = $id;
            $comment->accountant_id = $accountant->id;
            $comment->text = $commentText;
            $comment->save();
            $data = [
                'user' => $accountant,
                'document' => Document::findOne(['id' => $id]),
            ];
            return
                $page = $this->renderPage($data, 'addComment');
            // $response = \Yii::$app->response;
            // $response->format = Response::FORMAT_JSON;
            // $response->data =
            return
                [
                    'status' => 'success',
                    'data' => $page
                ];
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
            $docsQuery = (new Query())
                ->select(['d.*'])
                ->distinct()
                ->from(['d' => Document::tableName()])
                ->where([
                    'OR',
                    // ['ilike', 'd.filename', $query],
                    ['ilike', 'd.ocr_text', $query],
                    ['ilike', 'd.summary', $query],
                    ['ilike', 'd.category', $query],
                ])
                ->limit(self::SUGGESTS_COUNT);

            $data = $docsQuery->all();
            $data = array_map(function ($item) {
                $item['name'] = $item['ocr_text'] ?? $item['summary'] ?? $item['category'];
                $item['name'] = explode('\n', $item['name'])[0];
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
