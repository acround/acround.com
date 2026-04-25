<?php

use app\controllers\BaseController;
use app\services\DictionaryService;
use app\services\SvgService;

/**
 * @var int $limit
 */

$limit = $limit ?? BaseController::PAGE_LENGTH;
$sortType =  'none';
$sortTypeSign = SvgService::SORT_ARROW_NONE;
$sortDueDate = 'none';
$sortDueDateSign = SvgService::SORT_ARROW_NONE;
if ($sorting !== null && is_array($sorting)) {
    foreach ($sorting as $sort) {
        if ($sort['field'] == 'category') {
            $sortType = $sort['value'];
            $sortTypeSign = match ($sort['value']) {
                'asc' => SvgService::SORT_ARROW_ASC,
                'desc' => SvgService::SORT_ARROW_DESC,
                default => SvgService::SORT_ARROW_NONE,
            };
        } elseif ($sort['field'] == 'due_date') {
            $sortDueDate = $sort['value'];
            $sortDueDateSign = match ($sort['value']) {
                'asc' => SvgService::SORT_ARROW_ASC,
                'desc' => SvgService::SORT_ARROW_DESC,
                default => SvgService::SORT_ARROW_NONE,
            };
        }
    }
}

// echo '<pre>';print_r($sorting);echo '</pre>';
// echo $sortDueDate,$sortDueDateSign,$sortType,$sortTypeSign;
?>
<table class="w-full caption-bottom text-sm">
    <thead class="bg-secondary/50 sticky top-0">
        <tr class="border-b transition-colors data-[state=selected]:bg-muted hover:bg-muted/50">
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-20">ID</th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('companyName', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                <button class="sorting" data-entity="task" data-field="category" data-sort="<?= $sortType ?>">
                    <?= DictionaryService::getWord('taskType', $user->lang) ?>
                    <span><?= $sortTypeSign ?></span>
                </button>
            </th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('status', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('priority', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                <button class="sorting" data-entity="task" data-field="due_date" data-sort="<?= $sortDueDate ?>">
                    <?= DictionaryService::getWord('dueDate', $user->lang) ?>
                    <span><?= $sortDueDateSign ?></span>
                </button>
            </th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('assignedTo', $user->lang) ?></th>
        </tr>
    </thead>
    <tbody class="">

        <?php
        foreach ($tasks as $task) {
            $company = $task->getCompany();
        ?>
            <tr class="task-row border-b data-[state=selected]:bg-muted cursor-pointer hover:bg-secondary/50 transition-colors<?php if ($task->status == 'done') echo ' bg-destructive/5'; ?>" data-task-id="<?= $task->id ?>">
                <td class="p-4 align-middle font-mono text-xs"><?= $task->id ?></td>
                <td class="p-4 align-middle">
                    <div>
                        <p class="font-medium text-sm"><?= $company->name ?></p>
                        <p class="text-xs text-muted-foreground"><?= $company->pib ?></p>
                    </div>
                </td>
                <td class="p-4 align-middle text-sm"><?= $task->category ?></td>
                <td class="p-4 align-middle"><span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border <?= $task->getStatusStyle() ?>"><?= $task->getStatusText($user->lang) ?></span></td>
                <?php
                $priorityWord = $task->getPriorityWord();
                $prioritySign = DictionaryService::$prioritySign[$task->priority];
                ?>
                <td class="p-4 align-middle"><span class="inline-flex items-center gap-1 text-xs font-medium text-destructive"><span><?= $prioritySign ?></span><?= DictionaryService::getWord($priorityWord, $user->lang) ?></span></td>
                <td class="p-4 align-middle">
                    <?php
                    if ($task->due_date > date('Y-m-d')) {
                    ?>
                        <div class="flex items-center gap-1 text-sm"><?= $task->due_date ?></div>
                    <?php
                    } else {
                    ?>
                        <div class="flex items-center gap-1 text-sm text-destructive font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert h-3.5 w-3.5">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" x2="12" y1="8" y2="12"></line>
                                <line x1="12" x2="12.01" y1="16" y2="16"></line>
                            </svg>
                            <?= $task->due_date ?>
                        </div>
                    <?php
                    }
                    $accountant = $task->getAccountant();
                    ?>
                </td>
                <td class="p-4 align-middle text-sm"><?= $accountant->firstname . ' ' . $accountant->lastname ?></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<div class="pagination">
    <?php
    $pages = floor($total / $limit);
    if ($total % $limit > 0) {
        $pages++;
    }
    if ($pages > 1) {
        for ($i = 1; $i <= $pages; $i++) {
    ?>
            <button data-page="<?= $i ?>" class="page-btn inline-block w-8 h-8 text-center leading-8 border rounded-md mx-1 <?= ($i == $page ? 'active bg-primary text-primary-foreground' : 'hover:bg-secondary')  ?>"><?= $i ?></button>
    <?php
        }
    }
    ?>
</div>