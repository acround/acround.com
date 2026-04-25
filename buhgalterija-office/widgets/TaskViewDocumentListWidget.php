<?php

namespace app\widgets;

use yii\base\Widget;
use app\models\Task;

class TaskViewDocumentListWidget extends Widget
{
    public $user;
    public $task;
    public $documents;

    public function run()
    {
        return $this->render('taskviewdocumentlist', [
            'user' => $this->user,
            'task' => $this->task,
            'documents' => $this->documents,
        ]);
    }
}
