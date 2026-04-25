<?php

use app\services\DictionaryService;
use app\models\Document;
use app\services\SvgService;

$type = 'image';
if ($document->mimetype == 'application/pdf') {
    $type = 'pdf';
} elseif (str_starts_with($document->mimetype, 'video/')) {
    $type = 'video';
} elseif (str_starts_with($document->mimetype, 'audio/')) {
    $type = 'audio';
} elseif (str_ends_with($document->mimetype, '/tiff')) {
    $type = 'tiff';
} elseif (str_starts_with($document->mimetype, 'image')) {
    $type = 'image';
} else {
    $type = 'file';
}

?>
<div class="rounded-lg border bg-card text-card-foreground shadow-sm lg:col-span-2">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="font-semibold tracking-tight text-lg"><?= DictionaryService::getWord('documentInformation', $user->lang) ?></h3>
    </div>
    <div class="p-6 pt-0 space-y-6">
        <div class="grid gap-4 grid-cols-3">
            <div class="space-y-1">
                <p class="text-sm text-muted-foreground"><?= DictionaryService::getWord('documentType', $user->lang) ?></p>
                <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80"><?= $document->getTypeName($user->lang) ?></div>
            </div>
            <div class="space-y-1">
                <p class="text-sm text-muted-foreground"><?= DictionaryService::getWord('documentStatus', $user->lang) ?></p>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border <?= $document->getStatusStyle() ?>"><?= $document->getStatusName($user->lang) ?></span>
            </div>
            <div class="space-y-1">
                <p class="text-sm text-muted-foreground"><?= DictionaryService::getWord('fileSize', $user->lang) ?></p>
                <p class="text-sm font-medium"><?= Document::getStaticLength($document->id, $user->lang) ?></p>
            </div>
        </div>
        <div data-orientation="horizontal" role="none" class="shrink-0 bg-border h-[1px] w-full"></div>
        <div class="flex items-center justify-center h-64 bg-muted rounded-lg border border-dashed border-border" style="height: 500px">
            <?php
            switch ($type) {
                case 'pdf':
                    // case 'image':
            ?>
                    <iframe style="width:100%;height:100%;border:none;overflow:auto" src="/document/file/<?= $document->id ?>"></iframe>
                <?php
                    break;
                case 'image':
                ?>
                    <iframe style="width:100%;height:100%;border:none;overflow:hidden" src="/document/image/<?= $document->id ?>"></iframe>
                    <!-- <img class="max-h-full max-w-full object-contain" src="/document/file/<?= $document->id ?>" alt="<?= $document->filename ?>" /> -->
                <?php
                    break;
                case 'tiff':
                ?>
                    <div id="tiffCanvas" style="width:100%;height:100%;border:none;overflow:auto" data-doc-id="<?= $document->id ?>""></div>
                <?php
                    break;
                default:
                ?>
                    <div class=" text-center space-y-2">
                        <?= SvgService::svg('file-text') ?>
                        <p class="text-sm font-medium"><?= $document->filename ?></p>
                        <p class="text-xs text-muted-foreground"> <?= Document::getStaticLength($document->id, $user->lang) ?></p>
                    </div>
            <?php
                    break;
            }
            ?>
        </div>
        <div data-orientation="horizontal" role="none" class="shrink-0 bg-border h-[1px] w-full"></div>
        <!-- <div class="grid gap-4 grid-cols-2">
            <div class="space-y-1">
                <h3 class="font-semibold tracking-tight text-lg"><?= DictionaryService::getWord('ocrSummary', $user->lang) ?></h3>
                <p><?php //= $document->summary ?? DictionaryService::getWord('notRecognized', $user->lang) ?></p>
            </div>
            <div class="space-y-1">
                <h3 class="font-semibold tracking-tight text-lg"><?= DictionaryService::getWord('ocrCategory', $user->lang) ?></h3>
                <p><?php //= $document->category ?? DictionaryService::getWord('notRecognized', $user->lang) ?></p>
            </div>
        </div> -->
        <div class="grid gap-4 grid-cols-1">
            <div class="space-y-1">
                <h3 class="font-semibold tracking-tight text-lg"><?= DictionaryService::getWord('ocr', $user->lang) ?></h3>
                <p><?= $document->ocr_text ? nl2br(str_replace('\n', '<br>', $document->ocr_text))  : DictionaryService::getWord('notRecognized', $user->lang) ?></p>
            </div>
        </div>
    </div>
</div>