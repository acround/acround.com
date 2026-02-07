<?php

use app\services\DictionaryService;
?>
<table class="w-full caption-bottom text-sm">
    <thead class="[&amp;_tr]:border-b bg-secondary/50 sticky top-0">
        <tr class="border-b transition-colors data-[state=selected]:bg-muted hover:bg-muted/50">
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0 w-20">ID</th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('companyName', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('taskType', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('status', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('priority', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('dueDate', $user->lang) ?></th>
            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0"><?= DictionaryService::getWord('assignedTo', $user->lang) ?></th>
        </tr>
    </thead>
    <tbody class="[&amp;_tr:last-child]:border-0">

        <?php
        foreach ($tasks as $task) {
            $company = $task->getCompany();
        ?>
            <tr class="task-row border-b data-[state=selected]:bg-muted cursor-pointer hover:bg-secondary/50 transition-colors<?php if ($task->status == 'done') echo ' bg-destructive/5'; ?>" data-task-id="<?= $task->id ?>">
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 font-mono text-xs"><?= $task->id ?></td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
                    <div>
                        <p class="font-medium text-sm"><?= $company->name ?></p>
                        <p class="text-xs text-muted-foreground"><?= $company->pib ?></p>
                    </div>
                </td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-sm"><?= $task->category ?></td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0"><span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border <?= $task->getStatusStyle() ?>"><?= $task->getStatusText($user->lang) ?></span></td>
                <?php
                $priorityWord = $task->getPriorityWord();
                $prioritySign = DictionaryService::$prioritySign[$task->priority];
                ?>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0"><span class="inline-flex items-center gap-1 text-xs font-medium text-destructive"><span><?= $prioritySign ?></span><?= DictionaryService::getWord($priorityWord, $user->lang) ?></span></td>
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0">
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
                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-sm"><?= $accountant->firstname . ' ' . $accountant->lastname ?></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>