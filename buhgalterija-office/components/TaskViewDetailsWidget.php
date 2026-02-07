<?php

namespace app\components;

use yii\base\Widget;
use app\models\Task;

class TaskViewDetailsWidget extends Widget
{
    public $user;
    public $task;

    public function run()
    {
        return $this->render('taskviewdetails', [
            'user' => $this->user,
            'task' => $this->task,
        ]);
    }
}
