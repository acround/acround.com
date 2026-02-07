<?php

namespace app\components;

use yii\base\Widget;

class TaskViewTopWidget extends Widget
{
    public $user;
    public $task;

    public function run()
    {
        return $this->render('taskviewtop', [
            'user' => $this->user,
            'task' => $this->task,
        ]);
    }
}
