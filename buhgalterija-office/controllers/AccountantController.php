<?php

namespace app\controllers;

use yii\web\Response;
use app\models\Accountant;
use app\models\Task;
use app\services\AuthService;

class AccountantController extends BaseController
{
    public function actionPage($id = 0)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $viewer = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($viewer->isValid()) {
            if ($id) {
                $accountant = Accountant::findOne($id);
            }
            $data = [
                'accountant' => $accountant ?? new Accountant(),
                'user' => $viewer,
            ];
            return $this->renderPage($data);
        } else {
            return $this->renderLogout();
        }
    }

    public function actionView($id = 0)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $viewer = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($viewer->isValid()) {
            $accountant = Accountant::findOne($id);
            if ($accountant) {
                $taskQuery = Task::find()->where(['accountant_id' => $accountant->id]);
                $tasks = $taskQuery->all();
                if (empty($tasks)) {
                    $tasks = [];
                }
                $data = [
                    'user' => $viewer,
                    'accountant' => $accountant,
                    'tasks' => $tasks,
                    'id' => $id,
                ];
                return $this->renderPage($data, 'view');
            } else {
            }
        } else {
            return $this->renderLogout();
        }
    }

    public function actionList()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $viewer = Accountant::findIdentityByAccessToken(['token' => $token]);
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        if ($viewer->isValid()) {
            if (AuthService::hasPermission($viewer, AuthService::PERMISSION_VIEW_ACCOUNTANTS)) {
                $accountantsQuery = Accountant::find()
                    ->where(['!=', 'rule', 'bot'])
                    ->orderBy(['lastname' => SORT_ASC, 'firstname' => SORT_ASC]);
                $accountants = $accountantsQuery->all();
                if (empty($accountants)) {
                    $accountants = [];
                }
                $response->data = [
                    'status' => 'success',
                    'code' => 200,
                    'data' => $this->renderAjax('list', [
                        'accountants' => $accountants,
                        'user' => $viewer,
                    ])
                ];
            } else {
                $response->data = [
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'You have no permissions'
                ];
            }
        } else {
            return $this->renderLogout();
        }
        return $response;
    }

    public function actionProfile()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $data = [
                'user' => $accountant,
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
            $data = [
                'user' => $accountant,
            ];
            return $this->renderPage($data, 'edit');
        } else {
            return $this->renderLogout();
        }
    }

    public function actionChange()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $viewer = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($viewer->isValid()) {
            if (AuthService::hasPermission($viewer, AuthService::PERMISSION_VIEW_ACCOUNTANTS)) {
                $id = $request->post('id');
                if ($id) {
                    $accountant = Accountant::findOne($id);
                }
                $data = [
                    'accountant' => $accountant ?? (new Accountant()),
                    'user' => $viewer,
                ];
                return $this->renderPage($data, 'change');
            } else {
                $response = \Yii::$app->response;
                $response->format = Response::FORMAT_JSON;
                $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
                $response->data = [
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'You have no permissions'
                ];
            }
        } else {
            return $this->renderLogout();
        }
    }

    public function actionWrite()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $viewer = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($viewer->isValid()) {
            if (AuthService::hasPermission($viewer, AuthService::PERMISSION_VIEW_ACCOUNTANTS)) {
                $id = $request->post('id');
                if ($id) {
                    $accountant = Accountant::findOne($id) ?? (new Accountant());
                } else {
                    $accountant = new Accountant();
                    $accountant->setPassword($request->post('password'));
                }
                $accountant->firstname = $request->post('firstname');
                $accountant->lastname = $request->post('lastname');
                $accountant->email = $request->post('email');
                $accountant->rule = $request->post('role');
                $accountant->lang = $request->post('lang');
                $accountant->save();
                if ($accountant->hasErrors()) {
                    $errors = $accountant->getErrors();
                    $response = \Yii::$app->response;
                    $response->format = Response::FORMAT_JSON;
                    $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
                    $response->data = [
                        'status' => 'error',
                        'code' => 400,
                        'message' => implode(', ', $errors),
                    ];
                } else {
                    $data = [
                        'accountant' => $accountant ?? (new Accountant()),
                        'user' => $viewer,
                    ];
                    return $this->renderPage($data);
                }
            } else {
                $response = \Yii::$app->response;
                $response->format = Response::FORMAT_JSON;
                $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
                $response->data = [
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'You have no permissions'
                ];
            }
        } else {
            return $this->renderLogout();
        }
    }

    public function actionSave($id = 0)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $errors = [];
            if ($accountant->id == $id) {
                $email = $request->post('email');
                $oldPassword = $request->post('old_password');
                $newPassword = $request->post('new_password');
                $confirmPassword = $request->post('confirm_password');

                if ($email) {
                    $accountant->email = $email;
                }

                if ($oldPassword && $newPassword && $confirmPassword) {
                    if ($accountant->getPassword() == \app\services\AuthService::encodePassword($oldPassword)) {
                        if ($newPassword == $confirmPassword) {
                            $accountant->setPassword($newPassword);
                        } else {
                            $errors[] = 'Password fields are incorrect';
                        }
                    }
                }
                $accountant->save();
            } else {
                $errors[] = 'You can edit only your profile';
            }
            $data = [
                'status' => empty($errors) ? 'success' : 'error',
                'errors' => $errors,
                'user' => $accountant,
            ];
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = $data;
            return $response;
        } else {
            return $this->renderLogout();
        }
    }
}
