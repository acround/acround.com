<?php

namespace app\components;

use app\models\Document;
use yii\base\Widget;

class DocViewInfoWidget extends Widget
{
    public $user;
    public $document;

    public function run()
    {
        return $this->render('docviewinfo', [
            'user' => $this->user,
            'document' => $this->document,
        ]);
    }
}
