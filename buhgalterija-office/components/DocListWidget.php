<?php

namespace app\components;

use yii\base\Widget;

class DocListWidget extends Widget
{
    public $user;
    public $company;
    public $documents;

    public function run()
    {
        if (!$this->documents) {
            $this->documents = [];
        }
        return $this->render('doclist', [
            'user' => $this->user,
            'docs' => $this->documents,
        ]);
    }
}
