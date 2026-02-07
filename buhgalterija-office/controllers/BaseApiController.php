<?php

namespace app\controllers;

use Yii;
use yii\web\Response;

class BaseApiController extends BaseController
{

    protected function getJson()
    {
        $jsonString = file_get_contents('php://input');
        return json_decode($jsonString, true);
    }

    public function beforeAction($action)
    {
        $token = $this->getUserToken();
        if ($token !== null) {
            $accountant = \app\models\Accountant::findOne(['token' => $token]);
            if ($accountant) {
                // Yii::$app->user->login($accountant);
            }
        }
        if (Yii::$app->user->isGuest) {
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->httpStatusCode = 401;
            $response = ['status' => 'error', 'message' => 'Invalid credentials', 'code' => 401];
            return $response;
        }
        return parent::beforeAction($action);
    }

}
