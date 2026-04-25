<?php

use app\controllers\BaseController;
use app\models\Document;
use app\services\DictionaryService;
use app\services\SvgService;

/**
 * @var $user app\model\Accountant
 * @var $docs [app\model\Document]
 * @var $doc app\model\Document
 * @var $limit int
 */

$limit = $limit ?? $filters['limit'] ?? BaseController::PAGE_LENGTH;

?>

<table class="w-full caption-bottom text-sm">
    <thead class="bg-secondary/50">
        <tr class="border-b transition-colors hover:bg-muted/50">
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('fileName', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('companyName', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('documentType', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('documentStatus', $user->lang) ?></th>
        </tr>
    </thead>
    <tbody class="">
        <?php
        foreach ($docs as $doc) {
        $class = $doc->status == Document::STATUS_ARCHIVED ? 'doc-no-open' : 'doc-row cursor-pointer';
        ?>
            <tr data-doc-id="<?= $doc->id ?>" class="<?= $class ?> border-b hover:bg-secondary/30 transition-colors">
                <td class="p-4 align-middle">
                    <div class="flex items-center gap-2">
                        <?= SvgService::svg('document') ?>
                        <div>
                            <p class="font-medium text-sm truncate max-w-[200px]"><?= $doc->filename ?></p>
                            <p class="text-xs text-muted-foreground"><?= Document::getStaticLength($doc->id) ?></p>
                        </div>
                    </div>
                </td>
                <td class="p-4 align-middle text-sm"><?= $doc->getCompany() ? $doc->getCompany()->name : DictionaryService::getWord('unknown', $user->lang) ?></td>
                <td class="p-4 align-middle text-sm"><?= DictionaryService::getWord('docType' . ucfirst($doc->getType()->name), $user->lang) ?></td>
                <td class="p-4 align-middle"><span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border <?= $doc->getStatusStyle() ?>"><?= DictionaryService::getWord($doc->getStatusName(), $user->lang) ?></span></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<div class="pagination">
    <?php
    $pages = floor($total / $limit);
    if (($total % $limit) > 0) {
        $pages++;
    }
    if ($pages > 1) {
        for ($i = 1; $i <= $pages; $i++) {
    ?>
            <button data-page="<?= $i ?>" class="page-btn inline-block w-8 h-8 text-center leading-8 border rounded-md mx-1 <?= ($i == $filters['page'] ? 'active bg-primary text-primary-foreground' : 'hover:bg-secondary')  ?>"><?= $i ?></button>
    <?php
        }
    }
    ?>
</div>