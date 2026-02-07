<?php

use app\services\DictionaryService;
?>
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col space-y-1.5 p-6">
        <h3 class="font-semibold tracking-tight text-lg"><?= DictionaryService::getWord('taskDetails', $user->lang) ?></h3>
    </div>
    <div class="p-6 pt-0 space-y-4">
        <div>
            <h4 class="text-sm font-medium text-muted-foreground mb-1"><?= DictionaryService::getWord('description', $user->lang) ?></h4>
            <p class="text-sm"><?= $task->request ?></p>
        </div>
        <div data-orientation="horizontal" role="none" class="shrink-0 bg-border h-[1px] w-full"></div>
        <div class="grid grid-cols-2 gap-4">
            <!-- <div class="flex items-center gap-3">
                                <div class="p-2 bg-secondary rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar h-4 w-4 text-muted-foreground">
                                        <path d="M8 2v4"></path>
                                        <path d="M16 2v4"></path>
                                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                        <path d="M3 10h18"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Период</p>
                                    <p class="text-sm font-medium">Октябрь 2024</p>
                                </div>
                            </div> -->
            <?php
            if ($task->due_date < date('Y-m-d')) {
            ?>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-destructive/10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4 text-destructive">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground"><?= DictionaryService::getWord('dueDate', $user->lang) ?></p>
                        <p class="text-sm font-medium text-destructive"><?= $task->due_date ?><span class="ml-2 text-xs">(<?= DictionaryService::getWord('overdue', $user->lang) ?>)</span></p>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4 text-muted-foreground">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground"><?= DictionaryService::getWord('dueDate', $user->lang) ?></p>
                        <p class="text-sm font-medium "><?= $task->due_date ?></p>
                    </div>
                </div>
            <?php
            }
            ?>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-secondary rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user h-4 w-4 text-muted-foreground">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground"><?= DictionaryService::getWord('assignedTo', $user->lang) ?></p>
                    <p class="text-sm font-medium">
                        <?php
                        $accountant = $task->getAccountant();
                        if ($accountant) {
                            echo $accountant->firstname . ' ' . $accountant->lastname;
                        } else {
                            echo DictionaryService::getWord('notAssigned', $user->lang);
                        }
                        ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-secondary rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4 text-muted-foreground">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground"><?= DictionaryService::getWord('lastUpdate', $user->lang) ?></p>
                    <p class="text-sm font-medium"><?= $task->updated_at ?></p>
                </div>
            </div>
        </div>
    </div>
</div>