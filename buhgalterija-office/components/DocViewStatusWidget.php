<?php

namespace app\components;

use app\models\Document;
use yii\base\Widget;

class DocViewStatusWidget extends Widget
{
    public $user;
    public $document;

    public function run()
    {
        return $this->render('docviewstatus', [
            'user' => $this->user,
            'document' => $this->document,
        ]);
    }
}
