<?php

use app\components\ButtonBackWidget;
use app\components\CompanyListWidget;
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
                <h1 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord('companies', $user->lang) ?></h1>
                <p class="text-muted-foreground mt-1"><?= count($companies) ?> <?= strtolower(DictionaryService::getWord('companies', $user->lang)) ?></p>
            </div>
        </div>
        <div id="company-filter-box" class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <?= SvgService::svg('search') ?>
                    <input id="search" type="search" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-10" placeholder="<?= DictionaryService::getWord('companySearch', $user->lang) ?>">
                </div>
                <button id="company-filters-button" class="filters-on-off-button inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                    <?= SvgService::svg('filters') ?>
                    <?= DictionaryService::getWord('filters', $user->lang) ?>
                </button>
                <button id="company-find-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <?= SvgService::svg('search-button') ?>
                    <?= DictionaryService::getWord('find', $user->lang) ?>
                </button>
                <button id="company-add-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <?= SvgService::svg('plus-white') ?>
                    <?= DictionaryService::getWord('addCompany', $user->lang) ?>
                </button>
            </div>
            <div class="filter-box hidden flex items-center gap-3 p-4 bg-secondary/50 rounded-lg animate-fade-in">
                <?= DictionaryService::getWord('status', $user->lang) ?>
                <select id="status-filters-select" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-40">
                    <option value=""><?= DictionaryService::getWord('all', $user->lang) ?></option>
                    <?php
                    foreach ($filterStatus as $row) {
                        $status = $row['status'];
                    ?>
                        <option value="<?= $status ?>"><?= DictionaryService::getWord('status' . ucfirst($status), $user->lang) ?></option>
                    <?php
                    }
                    ?>
                </select>
                <?= DictionaryService::getWord('responsibleAccountant', $user->lang) ?>
                <select id="responsible-filters-select" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-48">
                    <option value=""><?= DictionaryService::getWord('all', $user->lang) ?></option>
                    <?php
                    foreach ($filterAccountant as $id => $row) {
                    ?>
                        <option value="<?= $row['id'] ?>"><?= $row['firstname'] . ' ' . $row['lastname'] ?></option>
                    <?php
                    }
                    ?>
                </select>
                <?= DictionaryService::getWord('sorting', $user->lang) ?>
                <select id="sorting-filters-select" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-48">
                    <option value=""><?= DictionaryService::getWord('all', $user->lang) ?></option>
                    <option value="name"><?= DictionaryService::getWord('byName', $user->lang) ?></option>
                    <option value="overdue"><?= DictionaryService::getWord('byOverdue', $user->lang) ?></option>
                    <option value="openTasks"><?= DictionaryService::getWord('byOpenTasks', $user->lang) ?></option>
                </select>
                <button class="reset-filters-button inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 ml-auto">
                    <?= SvgService::svg('x') ?>
                    <?= DictionaryService::getWord('clearFilters', $user->lang) ?>
                </button>
            </div>
        </div>
        <div id="company-list" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <?= CompanyListWidget::widget(['user' => $user, 'companies' => $companies]) ?>
        </div>
    </div>
</div>
<!-- </div> -->