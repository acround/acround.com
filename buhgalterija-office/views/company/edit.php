<?php

use app\components\ButtonBackWidget;
use app\components\SelectWidget;
use app\services\DictionaryService;
use app\services\SvgService;

?>
<div class="p-6">
    <?= ButtonBackWidget::widget(['user' => $user]) ?>
    <h1 class="text-2xl font-heading font-bold"><?= DictionaryService::getWord('companyEditing', $user->lang) ?></h1>
    <div class="space-y-6">
        <table id="company-edit-table" class="w-full caption-bottom text-sm">
            <input type="hidden" name="id" value="<?= $company->id ?>" />
            <tbody class="">
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('companyName', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle "><input type="text" name="name" value="<?= $company->name ?>" class="flex h-10 w-full rounded-md border border-input px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm " style="width:100%;" /></td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('companyNameTg', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle "><input type="text" name="name_tg" value="<?= $company->name_tg ?>" class="flex h-10 w-full rounded-md border border-input px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm " style="width:100%;" /></td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('companyType', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <?php
                        $options = [];
                        foreach ($companyTypes as $type) {
                            $options[] = [
                                'id' => $type->id,
                                'name' => $type->name,
                            ];
                        }
                        echo SelectWidget::widget(['user' => $user, 'id' => 'type', 'options' => $options, 'selected' => $company->type_id]);
                        ?>
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('status', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <?php
                        $options = [];
                        foreach ($companyStatuses as $status) {
                            $options[] = [
                                'id' => $status,
                                'name' => DictionaryService::getWord('status' . ucfirst($status), $user->lang),
                            ];
                        }
                        echo SelectWidget::widget(['user' => $user, 'id' => 'status', 'options' => $options, 'selected' => $company->status]);
                        ?>
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('PDV', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <input type="checkbox" name="is_pdv" class="pl-10" <?= $company->is_pdv ? 'checked="checked"' : '' ?> />
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('sector', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <?php
                        $options = [];
                        foreach ($companySector as $sector) {
                            $options[] = [
                                'id' => $sector->id,
                                'name' => ucfirst($sector->name),
                            ];
                        }
                        echo SelectWidget::widget(['user' => $user, 'id' => 'activity', 'options' => $options, 'selected' => $company->activity_id]);
                        ?>
                    </td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('pib', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle "><input type="text" name="pib" value="<?= $company->pib ?>" class="flex h-10 w-full rounded-md border border-input px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 md:text-sm " /></td>
                </tr>
                <tr class="border-b">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"><?= DictionaryService::getWord('responsibleAccountant', $user->lang) ?></th>
                    <td class="p-4 text-left align-middle ">
                        <?php
                        $options = [
                            [
                                'id' => 0,
                                'name' => DictionaryService::getWord('no', $user->lang),
                            ]
                        ];
                        $accountantId = 0;
                        $accountant = $company->getCompanyAccountants()->count() ? $company->getCompanyAccountants()->one() : [];
                        if ($accountant) {
                            $accountantId = $accountant->id;
                        }
                        foreach ($accountants as $accountant) {
                            $options[] = [
                                'id' => $accountant->id,
                                'name' => $accountant->firstname . ' ' . $accountant->lastname,
                            ];
                        }
                        echo SelectWidget::widget(['user' => $user, 'id' => 'accountant', 'options' => $options, 'selected' => $accountantId]);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="text-right">
                        <button id="company-save-button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2" data-id="<?= $company['id'] ?>">
                            <?= SvgService::svg('save') ?>
                            <?= DictionaryService::getWord('save', $user->lang) ?>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>