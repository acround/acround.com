<?php

use app\services\DictionaryService;
?>
<div id="doc-comment-block" class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="font-semibold tracking-tight text-base"><?= DictionaryService::getWord('comments', $user->lang) ?></h3>
    </div>
    <div id="doc-comments-list" class="px-6 pb-6 space-y-4 max-h-96 overflow-y-auto">
        <?php
        foreach ($comments as $comment) {
            $commentUser = $comment->getAccountant();
            $date = $comment->created_at;
            $date = date('d.m.Y', strtotime($date));
            $time = date('H:i', strtotime($date));
            $icon = strtoupper(substr($commentUser->firstname, 0, 1) . substr($commentUser->lastname, 0, 1));
            $timeAgo = Yii::$app->formatter->asRelativeTime($comment->created_at);

        ?>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-medium"><?= $icon ?></div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2"><span class="text-sm font-medium"><?= $commentUser->firstname, ' ', $commentUser->lastname ?></span><span class="text-xs text-muted-foreground"><?= $timeAgo ?></span></div>
                        <p class="text-sm text-muted-foreground mt-1"><?= $comment->text ?></p>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
    <div data-orientation="horizontal" role="none" class="shrink-0 bg-border h-[1px] w-full my-4"></div>
    <div class="flex gap-2">
        <input id="doc-comment-input" type="text" placeholder="<?= DictionaryService::getWord('addComment', $user->lang) ?>…" class="flex-1 px-3 py-2 text-sm border rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-primary/20">
        <button id="doc-send-comment" data-document-id="<?= $document->id ?>" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 w-10">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send h-4 w-4">
                <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"></path>
                <path d="m21.854 2.147-10.94 10.939"></path>
            </svg>
        </button>
    </div>
</div>
</div>