<?php

use app\models\Document;
use app\services\DictionaryService;
use app\services\SvgService;
use Codeception\Lib\Di;

/**
 * @var $user app\model\Accountant
 * @var $docs [app\model\Document]
 * @var $doc app\model\Document
 */

?>

<table class="w-full caption-bottom text-sm">
    <thead class="[&amp;_tr]:border-b bg-secondary/50">
        <tr class="border-b transition-colors data-[state=selected]:bg-muted hover:bg-muted/50">
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('fileName', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('companyName', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('documentType', $user->lang) ?></th>
            <!-- <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('period', $user->lang)  ?></th> -->
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('documentStatus', $user->lang) ?></th>
            <!-- <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('uploadedBy', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('uploadDate', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0 w-12"></th> -->
        </tr>
    </thead>
    <tbody class="[&amp;_tr:last-child]:border-0">
        <?php
        foreach ($docs as $doc) {
        ?>
            <tr data-doc-id="<?= $doc->id ?>" class="doc-row border-b data-[state=selected]:bg-muted hover:bg-secondary/30 transition-colors cursor-pointer">
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                    <div class="flex items-center gap-2">
                        <?= SvgService::svg('document') ?>
                        <div>
                            <p class="font-medium text-sm truncate max-w-[200px]"><?= $doc->filename ?></p>
                            <p class="text-xs text-muted-foreground"><?= Document::getStaticLength($doc->id) ?></p>
                        </div>
                    </div>
                </td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-sm"><?= $doc->getCompany() ? $doc->getCompany()->name : DictionaryService::getWord('unknown', $user->lang) ?></td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-sm"><?= DictionaryService::getWord('docType' . ucfirst($doc->getType()->name), $user->lang) ?></td>
                <!-- <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-sm text-muted-foreground">Q3 2024</td> -->
                <?php
                switch ($doc->status) {
                    case 'uploaded':
                        $statusClass = 'bg-info/10 text-info border-info/20';
                        break;
                    case 'checked':
                        $statusClass = 'bg-success/10 text-success border-success/20';
                        break;
                    case 'needsRevision':
                        $statusClass = 'bg-warning/10 text-warning border-warning/20';
                        break;
                    default:
                        $statusClass = 'bg-secondary/10 text-secondary border-secondary/20';
                }
                ?>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0"><span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border <?= $statusClass ?>"><?= DictionaryService::getWord('docStatus' . ucfirst($doc->status), $user->lang) ?></span></td>
                <!-- <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-sm">Ольга Сидорова</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-sm text-muted-foreground">28.10.2024</td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                    <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-8 w-8" type="button" id="radix-:r183:" aria-haspopup="menu" aria-expanded="false" data-state="closed">
                        <?= SvgService::svg('elipsis') ?>
                    </button>
                </td> -->
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>