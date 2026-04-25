<?php

use app\models\Document;
use app\services\DictionaryService;
use app\services\SvgService;

if (!empty($documents)) {
    foreach ($documents as $document) {
        $class = $document->status == Document::STATUS_ARCHIVED ? 'document-no-open' : 'document-view';
        if ($document->status == Document::STATUS_ARCHIVED) {
            $comments = $document->getComments()->all();
        } else {
            $comments = [];
        }
?>
        <div class="space-y-2">
            <div class="<?= $class ?> flex items-center justify-between p-3 rounded-lg border hover:bg-secondary/50 transition-colors" href="/document/view" data-doc-id="<?= $document->id ?>">
                <input type="hidden" class="document" value="<?= $document->id ?>" />
                <div class="flex items-center gap-3">
                    <?= SvgService::svg('document-gray') ?>
                    <div>
                        <p class="text-sm font-medium"><?= $document->filename ?></p>
                        <p class="text-xs text-muted-foreground"><?= Document::getStaticLength($document->id, $user->lang) ?></p>
                    </div>
                </div>
                <?php
                if ($comments) {
                ?>
                    <div>
                        <h4><?= DictionaryService::getWord('comments', $user->lang) ?></h4>
                        <?php
                        foreach ($comments as $comment) {
                            $accountant = $comment->getAccountant();
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
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-medium"><?= $commentUser->firstname, ' ', $commentUser->lastname ?></span>
                                            <span class="text-xs text-muted-foreground"><?= $timeAgo ?></span>
                                        </div>
                                        <p class="text-xs text-muted-foreground mt-1"><?= $comment->text ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
<?php
    }
}
