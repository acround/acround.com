<?php

namespace app\controllers;

use yii\web\Response;
use yii\db\Query;
use app\components\DocViewActivityWidget;
use app\components\DocListWidget;
use app\models\Accountant;
use app\models\Company;
use app\models\Document;
use app\models\DocumentComment;
use app\models\Task;
use app\models\TaskDocument;
use Exception;

class DocumentController extends BaseController
{

    public function getDataForPage($accountant, $status = null)
    {
        $docsQuery = Document::find();
        if ($accountant->rule !== 'ceo') {
            $docsQuery
                ->leftJoin(['td' => TaskDocument::tableName()], 'documents.id = td.document_id')
                ->leftJoin(['t' => Task::tableName()], 'td.task_id = t.id')
                ->leftJoin(['a' => Accountant::tableName()], 't.accountant_id = a.id')
                ->where('t.accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
        }
        if ($status !== null) {
            $docsQuery->andWhere(['documents.status' => $status]);
        }
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
            ->innerJoin(['t' => 'document_types'], 't.id = d.type_id')
            ->orderBy('t.name ASC');

        $filterStatusQuery = (new Query())
            ->select('status')
            ->distinct()
            ->from(Document::tableName());

        $data = [
            'user' => $accountant,
            'documents' => $docs,
            'status' => $status,
            'filterCompany' => $filterCompanyQuery->all(),
            'filterDocumentType' => $filterDocumentTypeQuery->all(),
            'filterStatus' => $filterStatusQuery->all(),
            'back' => $status !== null,
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
            $data = $this->getDataForPage($accountant, $status);
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
            $name = $request->post('name');
            $status = $request->post('status');
            $priority = $request->post('priority');
            $company = $request->post('company');
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
            if ($name) {
                $docsQuery->andWhere([
                    'OR',
                    ['ilike', 'd.filename', $name],
                    ['ilike', 'd.ocr_text', $name],
                    ['ilike', 'd.summary', $name],
                    ['ilike', 'd.category', $name],
                ]);
            }
            if ($status) {
                $docsQuery->andWhere(['d.status' => $status]);
            }
            if ($priority) {
                $docsQuery->andWhere(['d.priority' => $priority]);
            }
            if ($company) {
                $docsQuery->andWhere(['d.company_id' => $company]);
            }
            $docs = $docsQuery->all();
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'data' => DocListWidget::widget(['user' => $accountant, 'documents' => $docs, 'company' => null]),
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
            if ($id && $file) {
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
    //     if ($document) {
    //         $response->format = Response::FORMAT_RAW;
    //         $response->headers->add('Content-Type', $document->mimetype);
    //         $response->headers->add('Content-Disposition', "inline; filename=\"{$document->filename}\"");
    //         $response->content = stream_get_contents($document->content);
    //         return $response;
    //     } else {
    //         throw new \yii\web\NotFoundHttpException('Document not found');
    //     }
    // }

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
}
