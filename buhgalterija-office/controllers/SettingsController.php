<?php

namespace app\controllers;

use app\components\SettingsCalendarBodyWidget;
use app\models\Accountant;
use app\models\TaxCalendar;
use yii\db\Query;

class SettingsController extends BaseController
{
    public function actionPage($month = null)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $data = [
                'user' => $accountant,
            ];
            return $this->renderPage($data);
        } else {
            return $this->renderLogout();
        }
    }

}
