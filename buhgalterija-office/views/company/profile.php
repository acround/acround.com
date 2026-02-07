<?php

use app\components\ButtonBackWidget;
use app\services\DictionaryService;
use app\components\TaskListWidget;
use app\components\DocListWidget;
use app\components\CompanyNotesWidget;
use app\components\CompanyChatWidget;
use app\components\CompanyRemindersListWidget;
use app\components\CompanyTopWidget;
use app\services\SvgService;

/**
 * @var $company app\models\Company
 * @var $activity app\models\CompanyActivities
 * @var $customers app\models\Customer[]
 * @var $user app\models\Accountant
 * @var $taskCount int
 * @var $tasks app\models\Task[]
 * @var $tasksOverdue int
 */
?>
<div class="p-6">
    <div class="space-y-6">
        <?= ButtonBackWidget::widget(['user' => $user]) ?>
        <?= CompanyTopWidget::widget(['user' => $user, 'company' => $company, 'customers' => $customers]) ?>
        <div dir="ltr" data-orientation="horizontal" class="space-y-4">
            <div role="tablist" aria-orientation="horizontal" class="inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground" tabindex="0" data-orientation="horizontal" style="outline: none;">
                <button id="company-overview" data-chat-close="yes" type="button" role="tab" aria-selected="true" aria-controls="rcompany-overview" data-state="active" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">
                    <?= SvgService::svg('company-overview') ?>
                    <?= DictionaryService::getWord('overview', $user->lang) ?>
                </button>
                <button id="company-tasks" data-chat-close="yes" type="button" role="tab" aria-selected="false" aria-controls="rcompany-tasks" data-state="inactive" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-todo h-4 w-4">
                        <rect x="3" y="5" width="6" height="6" rx="1"></rect>
                        <path d="m3 17 2 2 4-4"></path>
                        <path d="M13 6h8"></path>
                        <path d="M13 12h8"></path>
                        <path d="M13 18h8"></path>
                    </svg>
                    <?= DictionaryService::getWord('tasks', $user->lang) ?> (<?= $taskCount ?>)
                </button>
                <button id="company-docs" data-chat-close="yes" type="button" role="tab" aria-selected="false" aria-controls="company-docs" data-state="inactive" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-4 w-4">
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                        <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                        <path d="M10 9H8"></path>
                        <path d="M16 13H8"></path>
                        <path d="M16 17H8"></path>
                    </svg>
                    <?= DictionaryService::getWord('documents', $user->lang) ?> (<?= $documentsCount ?>)
                </button>
                <button id="company-reminders" data-chat-close="yes" type="button" role="tab" aria-selected="false" aria-controls="company-reminders" data-state="inactive" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-4 w-4">
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                        <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                        <path d="M10 9H8"></path>
                        <path d="M16 13H8"></path>
                        <path d="M16 17H8"></path>
                    </svg>
                    <?= DictionaryService::getWord('reminders', $user->lang) ?>
                </button>
                <button id="company-notes" data-chat-close="yes" type="button" role="tab" aria-selected="false" aria-controls="company-notes" data-state="inactive" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square h-4 w-4">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <?= DictionaryService::getWord('notes', $user->lang) ?> (<?= $company->getNotesNumber() ?>)
                </button>
                <button id="company-chat" type="button" role="tab" aria-selected="false" aria-controls="company-chat" data-state="inactive" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2" tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-4 w-4">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <?= DictionaryService::getWord('chat', $user->lang) ?>
                </button>
            </div>
            <div data-state="active" data-orientation="horizontal" role="tabpanel" aria-labelledby="company-overview" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-card rounded-xl border p-5">
                        <h3 class="font-medium text-muted-foreground text-sm"><?= DictionaryService::getWord('sector', $user->lang) ?></h3>
                        <p class="mt-1 font-semibold"><?= $activity->name ?? '' ?></p>
                    </div>
                    <!-- <div class="bg-card rounded-xl border p-5">
                        <h3 class="font-medium text-muted-foreground text-sm">Ответственный бухгалтер</h3>
                        <p class="mt-1 font-semibold">Ольга Сидорова</p>
                    </div> -->
                    <div class="bg-card rounded-xl border p-5">
                        <h3 class="font-medium text-muted-foreground text-sm"><?= DictionaryService::getWord('openTasks', $user->lang) ?></h3>
                        <p class="mt-1 font-semibold"><?= $taskCount ?>
                            <?php
                            if ($tasksOverdue) {
                            ?>
                                <span class="text-destructive ml-2">(<?= $tasksOverdue ?> <?= strtolower(DictionaryService::getWord('overdue', $user->lang)) ?>)</span>
                            <?php
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div data-state="inactive" data-orientation="horizontal" role="tabpanel" aria-labelledby="company-tasks" id="company-content-tasks" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" hidden="">
                <div class="space-y-4">
                    <div class="border rounded-lg overflow-hidden">
                        <div class="relative w-full overflow-auto">
                            <?= TaskListWidget::widget(['user' => $user, 'company' => $company, 'tasks' => $tasks]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div data-state="inactive" data-orientation="horizontal" role="tabpanel" aria-labelledby="company-reminders" id="company-content-reminders" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" hidden="">
                <div class="border rounded-lg overflow-hidden">
                    <div class="relative w-full overflow-auto">
                        <?= CompanyRemindersListWidget::widget(['user' => $user, 'company' => $company,]); ?>
                    </div>
                </div>
            </div>
            <div data-state="inactive" data-orientation="horizontal" role="tabpanel" aria-labelledby="company-docs" id="company-content-documents" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" hidden="">
                <div class="border rounded-lg overflow-hidden">
                    <div class="relative w-full overflow-auto">
                        <?= DocListWidget::widget(['user' => $user, 'company' => $company, 'documents' => $documents]); ?>
                    </div>
                </div>
            </div>
            <div data-state="inactive" data-orientation="horizontal" role="tabpanel" aria-labelledby="company-notes" id="company-content-notes" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" hidden="">
                <?= CompanyNotesWidget::widget(['user' => $user, 'company' => $company]); ?>
            </div>
            <div data-state="inactive" data-orientation="horizontal" role="tabpanel" aria-labelledby="company-chat" id="company-content-chat" tabindex="0" class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" hidden="">
                <?= CompanyChatWidget::widget(['user' => $user, 'company' => $company]); ?>
            </div>
        </div>
    </div>
</div>
