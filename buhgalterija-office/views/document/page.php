<?php

use app\components\ButtonBackWidget;
use app\components\DocListWidget;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<div class="p-6">
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <?php
            if ($back) {
            ?>
                <?= ButtonBackWidget::widget(['user' => $user]) ?>
            <?php
            }
            ?>
            <div>
                <h1 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord('documents', $user->lang) ?></h1>
                <p class="text-muted-foreground mt-1">5 <?= strtolower(DictionaryService::getWord('documents', $user->lang)) ?></p>
            </div>
        </div>
        <!-- @todo Фильтры для документов -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 max-w-md">
                <?= SvgService::svg('search') ?>
                <input id="search" type="search" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-10" placeholder="Поиск документы...">
            </div>
            <?= DictionaryService::getWord('companyName', $user->lang) ?>
            <select id="companyName-filters-select" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-40">
                <option value=""><?= DictionaryService::getWord('all', $user->lang) ?></option>
                <?php
                foreach ($filterCompany as $company) {
                ?>
                    <option value="<?= $company['id'] ?>"><?= $company['name'] ?></option>
                <?php
                }
                ?>
            </select>
            <?= DictionaryService::getWord('documentType', $user->lang) ?>
            <select id="documentType-filters-select" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-40">
                <option value=""><?= DictionaryService::getWord('all', $user->lang) ?></option>
                <?php
                foreach ($filterDocumentType as $company) {
                ?>
                    <option value="<?= $company['id'] ?>"><?= $company['name'] ?></option>
                <?php
                }
                ?>
            </select>
            <?= DictionaryService::getWord('status', $user->lang) ?>
            <select id="status-filters-select" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-40">
                <option value=""><?= DictionaryService::getWord('all', $user->lang) ?></option>
                <?php
                foreach ($filterStatus as $row) {
                    $status = $row['status'];
                ?>
                    <option value="<?= $status ?>"><?= DictionaryService::getWord('docStatus' . ucfirst($status), $user->lang) ?></option>
                <?php
                }
                ?>
            </select>
            <button id="doc-find-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                <?= SvgService::svg('plus-white') ?>
                <?= DictionaryService::getWord('find', $user->lang) ?>
            </button>
        </div>
        <div class="border rounded-lg overflow-hidden">
            <div id="doc-list" class="relative w-full overflow-auto">
                <?= DocListWidget::widget(['user' => $user, 'company' => null, 'documents' => $documents]); ?>
            </div>
        </div>
    </div>
</div>