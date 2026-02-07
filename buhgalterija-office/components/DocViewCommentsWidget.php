<?php

namespace app\components;

use app\models\Document;
use yii\base\Widget;

class DocViewCommentsWidget extends Widget
{
    public $user;
    public $document;
    public $comments;

    public function run()
    {
        if (!$this->comments) {
            $this->comments = $this->document->getComments()->orderBy(['created_at' => SORT_DESC])->all();
        }
        return $this->render('docviewcomments', [
            'user' => $this->user,
            'document' => $this->document,
            'comments' => $this->comments,
        ]);
    }
}
