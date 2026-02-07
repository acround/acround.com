<?php

namespace app\components;

use app\models\CompanyNotes;
use yii\base\Widget;

class CompanyNotesWidget extends Widget
{
    public $user;
    public $company;

    public function run()
    {
        $notes = CompanyNotes::find(['company_id' => $this->company->id, 'status' => 'active'])->limit(10)->orderBy('id DESC')->all();
        return $this->render('companynotes', [
            'user' => $this->user,
            'notes' => $notes,
            'company' => $this->company,
        ]);
    }
}
