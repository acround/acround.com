<?php

namespace app\components;

use app\models\Document;
use yii\base\Widget;

class DocViewTopWidget extends Widget
{
    public $user;
    public $document;

    public function run()
    {
        return $this->render('docviewtop', [
            'user' => $this->user,
            'document' => $this->document,
        ]);
    }
}
