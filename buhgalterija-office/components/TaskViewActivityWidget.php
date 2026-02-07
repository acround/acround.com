<?php

namespace app\components;

use yii\base\Widget;

class TaskViewActivityWidget extends Widget
{
    public $user;
    public $task;

    public function run()
    {
        return $this->render('taskviewactivity', [
            'user' => $this->user,
            'task' => $this->task,
        ]);
    }
}
