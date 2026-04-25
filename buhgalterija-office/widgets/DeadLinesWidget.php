<?php

namespace app\widgets;

use yii\base\Widget;

class DeadLinesWidget extends Widget
{
    public $viewAccountants;
    public $data;
    public $user;

    public function run()
    {
        return $this->render('deadline', [
            'viewAccountants' => $this->viewAccountants,
            'data' => $this->data,
            'user'=> $this->user,
        ]);
    }
}
