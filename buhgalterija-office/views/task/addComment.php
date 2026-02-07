<?php

use app\components\TaskCommentListWidget;

$comments = $task->getComments();
echo TaskCommentListWidget::widget([
    'task' => $task,
    'user' => $user,
    'comments' => $comments,
]);
