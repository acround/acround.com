<?php

namespace app\widgets;

use app\models\Document;
use yii\base\Widget;

class ButtonBackWidget extends Widget
{
    public $user;

    public function run()
    {
        return $this->render('buttonback', [
            'user' => $this->user,
        ]);
    }
}
