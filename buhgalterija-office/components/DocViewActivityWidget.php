<?php

namespace app\components;

use app\models\Document;
use yii\base\Widget;

class DocViewActivityWidget extends Widget
{
    public $user;
    public $document;

    public function run()
    {
        $activities = $this->document->getActivities()->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('docviewactivity', [
            'user' => $this->user,
            'activities' => $activities,
        ]);
    }
}
