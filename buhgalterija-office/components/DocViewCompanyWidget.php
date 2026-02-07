<?php

namespace app\components;

use app\models\Document;
use yii\base\Widget;

class DocViewCompanyWidget extends Widget
{
    public $user;
    public $document;

    public function run()
    {
        return $this->render('docviewcompany', [
            'user' => $this->user,
            'document' => $this->document,
        ]);
    }
}
