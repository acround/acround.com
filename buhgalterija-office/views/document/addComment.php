<?php

use app\components\DocViewCommentsWidget;

$comments = $document->getComments()->all();
echo DocViewCommentsWidget::widget([
    'document' => $document,
    'user' => $user,
    'comments' => $comments,
]);
