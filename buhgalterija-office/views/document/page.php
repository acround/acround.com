<?php

use app\widgets\DocListWidget;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<div class="p-6">
    <div class="space-y-6">
        <div id="page-header" class="flex items-center gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord('documents', $user->lang) ?></h1>
                <p class="text-muted-foreground mt-1"><span id="entity-count"><?= strtolower(DictionaryService::getWord('documents', $user->lang)) ?>: <?= $total ?></span></p>
            </div>
        </div>
        <div class="all-filter-box" data-entity="document">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <div class="suggest-container">
                        <?= SvgService::svg('search') ?>
                        <input id="search" type="search" data-type="document" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm pl-10" placeholder="<?= DictionaryService::getWord('documentSearch', $user->lang) ?>" value="<?= $filters['name'] ?>">
                        <input type="hidden" id="selected_id">
                        <div id="suggestions" class="suggestions"></div>
                    </div>
                </div>
                <button id="doc-find-button" class="find-button inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50    bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <?= SvgService::svg('plus-white') ?>
                    <?= DictionaryService::getWord('find', $user->lang) ?>
                </button>
            </div>
            <div class="filter-box flex items-center gap-3 p-4 bg-secondary/50 rounded-lg animate-fade-in">
                <?= DictionaryService::getWord('companyName', $user->lang) ?>
                <select id="companyName-filters-select" data-field="company" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50  w-40">
                    <option value=""><?= DictionaryService::getWord('all', $user->lang) ?></option>
                    <?php
                    foreach ($filterCompany as $company) {
                        $select = $filters['company'] == $company['id'] ? ' selected' : '';
                    ?>
                        <option value="<?= $company['id'] ?>" <?= $select ?>><?= $company['name'] ?></option>
                    <?php
                    }
                    ?>
                </select>
                <?= DictionaryService::getWord('documentType', $user->lang) ?>
                <select id="documentType-filters-select" data-field="type" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50  w-40">
                    <option value=""><?= DictionaryService::getWord('all', $user->lang) ?></option>
                    <?php
                    foreach ($filterDocumentType as $type) {
                        $select = $filters['type'] == $type['id'] ? ' selected' : '';
                    ?>
                        <option value="<?= $type['id'] ?>" <?= $select ?>><?= DictionaryService::getWord('docType' . ucfirst($type['name']), $user->lang) ?></option>
                    <?php
                    }
                    ?>
                </select>
                <?= DictionaryService::getWord('status', $user->lang) ?>
                <select id="status-filters-select" data-field="status" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50  w-40">
                    <option value=""><?= DictionaryService::getWord('all', $user->lang) ?></option>
                    <?php
                    foreach ($filterStatus as $row) {
                        $status = $row['status'];
                        $select = $filters['status'] == $status ? ' selected' : '';
                    ?>
                        <option value="<?= $status ?>" <?= $select ?>><?= DictionaryService::getWord('docStatus' . str_replace(' ', '', ucwords(str_replace('_', ' ', $status))), $user->lang) ?></option>
                    <?php
                    }
                    ?>
                </select>
                <button class="reset-filters-button inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3">
                    <?= SvgService::svg('x') ?>
                    <?= DictionaryService::getWord('clearFilters', $user->lang) ?>
                </button>
            </div>
        </div>
    </div>
    <div class="border rounded-lg overflow-hidden">
        <div id="entity-list" class="list-block relative w-full overflow-auto">
            <?= DocListWidget::widget(['user' => $user, 'company' => null, 'documents' => $documents, 'filters' => $filters, 'total' => $total, 'limit' => $limit]); ?>
        </div>
    </div>
</div>
</div>