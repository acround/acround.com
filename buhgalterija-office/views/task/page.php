<?php

use app\components\ButtonBackWidget;
use app\components\TaskListWidget;
use app\services\AuthService;
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
                <h1 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord('tasks', $user->lang) ?></h1>
                <p class="text-muted-foreground mt-1">8 <?= strtolower(DictionaryService::getWord('tasks', $user->lang)) ?></p>
            </div>
        </div>
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <?= SvgService::svg('search') ?>
                    <input id="search" type="search" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-10" placeholder="<?= DictionaryService::getWord('taskSearch', $user->lang) ?>">
                </div>
                <button class="filters-on-off-button inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 bg-secondary">
                    <?= SvgService::svg('filters') ?>
                    <?= DictionaryService::getWord('filters', $user->lang) ?>
                </button>
                <button id="task-find-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <?= SvgService::svg('search-button') ?>
                    <?= DictionaryService::getWord('find', $user->lang) ?>
                </button>
                <button id="task-add-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <?= SvgService::svg('plus-white') ?>
                    <?= DictionaryService::getWord('addTask', $user->lang) ?>
                </button>
            </div>
            <div class="filter-box hidden flex flex-wrap items-center gap-3 p-4 bg-secondary/50 rounded-lg animate-fade-in">
                <?= DictionaryService::getWord('status', $user->lang) ?>
                <select id="status-filters-select" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-40">
                    <option value=""><?= DictionaryService::getWord('all', $user->lang) ?></option>
                    <?php
                    foreach ($filterStatus as $row) {
                        $status = $row['status'];
                    ?>
                        <option value="<?= $status ?>"><?= DictionaryService::getWord('taskStatus' . ucfirst($status), $user->lang) ?></option>
                    <?php
                    }
                    ?>
                </select>
                <?= DictionaryService::getWord('priority', $user->lang) ?>
                <select id="priority-filters-select" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-40">
                    <option value=""><?= DictionaryService::getWord('all', $user->lang) ?></option>
                    <?php
                    foreach ($filterPriority as $row) {
                        $prioriry = $row['priority'];
                    ?>
                        <option value="<?= $prioriry ?>"><?= DictionaryService::getWord('priority' . ucfirst($prioriry), $user->lang) ?></option>
                    <?php
                    }
                    ?>
                </select>
                <?php
                if (AuthService::hasPermission($user, 'viewAccountants')) {
                ?>
                    <?= DictionaryService::getWord('assignedTo', $user->lang) ?>
                    <select id="assignedTo-filters-select" class="flex h-10 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-40">
                        <option value=""><?= DictionaryService::getWord('all', $user->lang) ?></option>
                        <?php
                        foreach ($filterAssignedTo as $accountant) {
                        ?>
                            <option value="<?= $accountant['id'] ?>"><?= $accountant['firstname'] . ' ' . $accountant['lastname'] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
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
                <button id="task-reset-filters-button" class="reset-filters-button inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 rounded-md px-3 ml-auto">
                    <?= SvgService::svg('x') ?>
                    <?= DictionaryService::getWord('clearFilters', $user->lang) ?>
                </button>
            </div>
        </div>
        <div class="space-y-4">
            <div class="border rounded-lg overflow-hidden">
                <div id="task-list" class="relative w-full overflow-auto">
                    <?= TaskListWidget::widget(['user' => $user, 'tasks' => $tasks, 'company' => null]); ?>
                </div>
            </div>
        </div>
    </div>
</div>