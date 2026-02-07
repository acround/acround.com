<?php

namespace app\components;

use yii\base\Widget;
use app\models\Task;

class TaskViewDocumentsWidget extends Widget
{
    public $user;
    public $task;

    public function run()
    {
        return $this->render('taskviewdocuments', [
            'user' => $this->user,
            'task' => $this->task,
        ]);
    }
}
