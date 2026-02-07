<?php

namespace app\components;

use yii\base\Widget;
use app\models\Task;

class TaskListWidget extends Widget
{
    public $user;
    public $company;
    public $tasks;

    public function run()
    {
        if (!$this->tasks && $this->company) {
            $tasksQuery = Task::find()->where(['company_id' => $this->company->id]);
            if ($this->user) {
                if ($this->user->rule != 'ceo') {
                    $tasksQuery->andWhere(['accountant_id' => $this->user->id]);
                }
            }
            $tasks = $tasksQuery->all();
            $this->tasks = $tasks;
        }

        return $this->render('tasklist', [
            'user' => $this->user,
            'company' => $this->company,
            'tasks' => $this->tasks,
        ]);
    }
}
