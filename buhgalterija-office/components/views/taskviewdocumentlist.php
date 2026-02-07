<?php

use app\models\Document;
use app\services\SvgService;

if (!empty($documents)) {
    foreach ($documents as $document) {
?>
        <div class="space-y-2">
            <a class="document-view flex items-center justify-between p-3 rounded-lg border hover:bg-secondary/50 transition-colors" href="/document/view" data-doc-id="<?= $document->id ?>">
                <input type="hidden" class="document" value="<?= $document->id ?>" />
                <div class="flex items-center gap-3">
                    <?= SvgService::svg('document-gray') ?>
                    <div>
                        <p class="text-sm font-medium"><?= $document->filename ?></p>
                        <p class="text-xs text-muted-foreground"><?= Document::getStaticLength($document->id, $user->lang) ?></p>
                    </div>
                </div>
            </a>
        </div>
<?php
    }
}
