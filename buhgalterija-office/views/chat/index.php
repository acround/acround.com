<?php

use app\services\DictionaryService;
use yii\helpers\Html;
use yii\helpers\Url;
//Нет активных чатов. Убедитесь, что бот добавлен в группы и webhook настроен.
$this->title = DictionaryService::getWord('chatList', $user->lang);
?>

<div class="chat-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <div id="chat-list-container" class="list-group">
    </div>
</div>