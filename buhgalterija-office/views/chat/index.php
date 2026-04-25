<?php

use app\services\DictionaryService;
use yii\helpers\Html;

$this->title = DictionaryService::getWord('chatList', $user->lang);
?>

<div class="chat-index grid md:grid-cols-2 xl:grid-cols-2">
    <div id="chat-list" class="chat-list bg-card p-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <div id="chat-list-container" class="list-group">
        </div>
    </div>
    <div id="chat-summary-container" class="chat-view bg-card p-4">
        <div id="chat-summary-placeholder" class="text-center text-gray-500">
            <button id="show-summary-btn" disabled="disabled" class="send-button chat-summary inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2" title="<?= Html::encode(DictionaryService::getWord('chatSummaryFor24Hours', $user->lang)) ?>"><?= Html::encode(DictionaryService::getWord('chatSummary', $user->lang)) ?></button>
        </div>
        <div id="chat-summary-content" class=""></div>
    </div>
</div>