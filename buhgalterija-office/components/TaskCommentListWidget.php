<?php

namespace app\components;

use yii\base\Widget;
use app\models\Task;

class TaskCommentListWidget extends Widget
{
    public $user;
    public $task;
    public $comments;

    public function run()
    {
        return $this->render('taskcommentlist', [
            'user' => $this->user,
            'task' => $this->task,
            'comments' => $this->comments,
        ]);
    }
}
