<?php

namespace app\controllers;

use app\models\Accountant;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UtilController extends BaseController
{
    public $layout = false;
    public $enableCsrfValidation = false;

    public function actionTranslate()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $data = [
                'text' => $request->post('text'),
                'from' => $request->post('from'),
                'to' => $request->post('to'),
            ];
            $response->data = $this->makeN8nWebhookCall('translate',$data);
            return $response;
        } else {
            return $this->renderLogout();
        }
    }
}
