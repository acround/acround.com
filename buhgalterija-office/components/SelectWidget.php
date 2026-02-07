<?php

namespace app\components;

use app\models\Document;
use yii\base\Widget;

class SelectWidget extends Widget
{
    public $user;
    public $id;
    public $options;
    public $selected;

    public function run()
    {
        return $this->render('select', [
            'user' => $this->user,
            'id' => $this->id,
            'options' => $this->options,
            'selected' => $this->selected,
        ]);
    }
}
