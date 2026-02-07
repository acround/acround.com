<?php

use app\models\Document;
use app\services\DictionaryService;

?>
<p><?= DictionaryService::getWord('currentStatus', $user->lang) ?>: <strong><?= $document->getStatusName($user->lang) ?></strong></p>
<button id="doc-change-status" data-doc-id="<?= $document->id ?>" class="inline-flex items-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full justify-start">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen mr-2 h-4 w-4">
        <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
        <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
    </svg>
    <?= DictionaryService::getWord('changeStatus', $user->lang) ?>
</button>
<?php
$statuses = [];
foreach (Document::$statuses as $status) {
    $s = $document->status == $status ? ' selected' : '';
    $statuses[] = '<option value="' . $status . '"' . $s . '>' . $document->getStatusName($user->lang, $status) . '</option>';
}
?>
<select id="doc-status-select" data-doc-id="<?= $document->id ?>" class="hidden w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
    <?= implode("\n", $statuses); ?>
</select>