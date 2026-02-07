<?php

namespace app\components;

use yii\base\Widget;
use app\models\Task;

class TaskViewDocumentListWidget extends Widget
{
    public $user;
    public $documents;

    public function run()
    {
        return $this->render('taskviewdocumentlist', [
            'user' => $this->user,
            'documents' => $this->documents,
        ]);
    }
}
