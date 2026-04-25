<?php

namespace app\widgets;

use yii\base\Widget;

class DocListWidget extends Widget
{
    public $user;
    public $company;
    public $documents;
    public $total;
    public $page;
    public $limit;
    public $filters;

    public function run()
    {
        if (!$this->documents) {
            $this->documents = [];
        }
        return $this->render('doclist', [
            'user' => $this->user,
            'docs' => $this->documents,
            'total' => $this->total,
            'page' => $this->page,
            'limit' => $this->limit,
            'filters' => $this->filters,
        ]);
    }
}
