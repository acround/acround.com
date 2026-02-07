<?php

namespace app\components;

use yii\base\Widget;

class TaskViewCompanyInfoWidget extends Widget
{
    public $user;
    public $task;

    public function run()
    {
        return $this->render('taskviewcompanyinfo', [
            'user' => $this->user,
            'task' => $this->task,
        ]);
    }
}
