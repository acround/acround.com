<?php

namespace app\components;

use yii\base\Widget;

class DocUploadBodyWidget extends Widget
{
    public $user;
    public $taskId;

    public function run()
    {
        return $this->render('docuploadbody', [
            'user' => $this->user,
            'taskId' => $this->taskId,
        ]);
    }
}
