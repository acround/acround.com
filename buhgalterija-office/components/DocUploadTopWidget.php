<?php

namespace app\components;

use yii\base\Widget;

class DocUploadTopWidget extends Widget
{
    public $user;
    public $document;

    public function run()
    {
        return $this->render('docuploadtop', [
            'user' => $this->user,
        ]);
    }
}
