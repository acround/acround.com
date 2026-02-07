<?php

use app\models\TelegramChat;
use app\models\TelegramTopic;

?>
<div class="grid md:grid-cols-2 xl:grid-cols-2">
    <div class="bg-card rounded-xl border p-6">
        <div class="flex items-center gap-4 pb-4 border-b">
            <?php
            echo $this->render('@app/views/chat/view', [
                'topic' => new TelegramTopic(),
                'chat' => new TelegramChat(),
                'messages' => [],
            ])
            ?>
        </div>
    </div>
    <div class="bg-card rounded-xl border p-6">
        <input type="hidden" id="customer-tg-id" value="<?= $customer['tg_id'] ?>">
        <div class="flex items-center gap-4 pb-4 border-b">
            <?php
            echo $this->render('@app/views/chat/index', [
                'user' => $user,
                'chats' => [],
            ])
            ?>
        </div>
    </div>
</div>