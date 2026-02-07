<?php

use app\services\DictionaryService;

?>
<div class="bg-card rounded-xl border p-5">
    <h3 class="font-heading font-semibold text-lg mb-4 flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-5 w-5 text-primary">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        <?= DictionaryService::getWord('workloadDistribution', $user->lang) ?>
    </h3>
    <div class="space-y-4">
        <?php
        $maxTasks = 0;
        foreach ($data['accountants'] as $accountant) {
            if ($maxTasks < $accountant['tasks']) {
                $maxTasks = $accountant['tasks'];
            }
        }
        $maxTasks = $maxTasks < 10 ? 10 : $maxTasks;
        foreach ($data['accountants'] as $accountant) {
            $percents = round($accountant['tasks'] / $maxTasks * 100, 2);
        ?>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="go-to-link text-sm font-medium truncate pr-2 cursor-pointer hover:underline" data-link="/accountant/view/<?= $accountant['id'] ?>"><?= $accountant['firstname'] . ' ' . $accountant['lastname'] ?></span>
                    <div class="flex items-center gap-2 flex-shrink-0"><span class="text-sm text-muted-foreground"><?= $accountant['tasks'] ?></span>
                        <?php
                        if (isset($accountant['overdueTasks'])) {
                            if ($accountant['overdueTasks']) {
                        ?>
                                <span class="text-xs text-destructive font-medium">(<?= $accountant['overdueTasks'] ?> !)</span>
                            <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="h-2 bg-secondary rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all bg-destructive" style="width: <?= $percents ?>%;"></div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>