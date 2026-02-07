<?php

namespace app\components;

use yii\base\Widget;
use app\models\Task;

class TaskViewCommentsWidget extends Widget
{
    public $user;
    public $task;

    public function run()
    {
        return $this->render('taskviewcomments', [
            'user' => $this->user,
            'task' => $this->task,
        ]);
    }
}
