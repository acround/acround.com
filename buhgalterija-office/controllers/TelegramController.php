<?php

namespace app\controllers;

use yii\web\Controller;
use Yii;

class TelegramController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'webhook' => ['post'],
                ],
            ],
        ];
    }

    public function actionWebhook()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($data) {
            Yii::$app->telegram->processWebhook($data);
        }

        return 'OK';
    }
}
