<?php

use app\widgets\DocViewCommentsWidget;

$comments = $document->getComments()->all();
echo DocViewCommentsWidget::widget([
    'document' => $document,
    'user' => $user,
    'comments' => $comments,
]);
