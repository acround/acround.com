<?php

namespace app\models;

use Yii;
use yii\db\Query;
use app\services\DictionaryService;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $accountant_id
 * @property string|null $category
 * @property string|null $request
 * @property int|null $due_date
 * @property string|null $status
 * @property string|null $priority
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Task extends \yii\db\ActiveRecord
{

    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'inProgress';
    const STATUS_WAITING = 'waiting';
    const STATUS_DONE = 'done';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CLOSED = 'closed';
    const STATUS_ARCHIVED  = 'archived';

    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';

    public $taskStatusStyles = [
        'overdue' =>   'bg-destructive/10 text-destructive border-destructive/20',
        'done' =>      'bg-success/10 text-success border-success/20',
        'waiting' =>   'bg-purple-100 text-purple-700 border-purple-200',
        'inProgress' => 'bg-warning/10 text-warning border-warning/20',
        'new' =>       'bg-info/10 text-info border-info/20',
    ];

    public static function getStatuses()
    {
        return [
            self::STATUS_NEW,
            self::STATUS_IN_PROGRESS,
            self::STATUS_WAITING,
            self::STATUS_DONE,
            self::STATUS_OVERDUE,
            self::STATUS_CLOSED,
            self::STATUS_ARCHIVED,
        ];
    }

    public static function getStatusesCompleted()
    {
        return [
            self::STATUS_DONE,
            self::STATUS_CLOSED,
            self::STATUS_ARCHIVED,
        ];
    }

    public static function getPriorities()
    {
        return [
            self::PRIORITY_LOW,
            self::PRIORITY_NORMAL,
            self::PRIORITY_HIGH,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    public static function getAnyPriorityText($priority, $lang = 'ru')
    {
        return $priority ? DictionaryService::getWord('priority' . ucfirst($priority), $lang) : '';
    }

    public static function getAnyStatusText($status, $lang = 'ru')
    {
        return $status ? DictionaryService::getWord('taskStatus' . ucfirst($status), $lang) : '';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'category', 'request', 'due_date'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['company_id', 'due_date'], 'default', 'value' => null],
            [['company_id', 'accountant_id'], 'integer'],
            [['category'], 'string', 'max' => 64],
            [['request'], 'string', 'max' => 256],
            [['status'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'category' => 'Category',
            'request' => 'Request',
            'due_date' => 'Due Date',
            'status' => 'Status',
        ];
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $taskActivity = new TaskActivity();
        $taskActivity->task_id = $this->id;
        $taskActivity->accountant_id = $this->accountant_id;
        if (!$this->isNewRecord) {
            $oldTask = Task::findOne(['id' => $this->id]);
            if ($oldTask && $oldTask->status !== $this->status) {
                $stepName = TaskStep::$steps[$this->status] ?? null;
                if ($stepName) {
                    $step = TaskStep::findOne(['name' => $stepName]);
                    if ($step) {
                        $taskActivity->step_id = $step->id;
                        $taskActivity->save();
                    }
                }
            } elseif ($oldTask && $oldTask->accountant_id !== $this->accountant_id) {
                $taskActivity->step_id = TaskStep::findOne(['name' => 'assigned'])->id;
                $taskActivity->save();
            } elseif ($oldTask && $oldTask->priority !== $this->priority) {
                $step = TaskStep::findOne(['name' => 'priority_changed']);
                if ($step) {
                    $taskActivity->step_id = $step->id;
                    $taskActivity->save();
                }
            } elseif ($oldTask && $oldTask->due_date !== $this->due_date) {
                $step = TaskStep::findOne(['name' => 'due_date_changed']);
                if ($step) {
                    $taskActivity->step_id = $step->id;
                    $taskActivity->save();
                }
            }
        }
        $ret = parent::save($runValidation, $attributeNames);
        if ($taskActivity->task_id === null) {
            $taskActivity->task_id = $this->id;
            $step = TaskStep::findOne(['name' => 'created']);
            if ($step) {
                $taskActivity->step_id = $step->id;
                $taskActivity->save();
            }
        }
        return $ret;
    }

    public function getAccountant()
    {
        return Accountant::findOne(['id' => $this->accountant_id]);
    }

    public function getCompany()
    {
        return Company::findOne(['id' => $this->company_id]);
    }

    public function getStatusStyle()
    {
        return $this->taskStatusStyles[$this->status] ?? '';
    }

    public function getPriorityWord()
    {
        return $this->priority ? 'priority' . ucfirst($this->priority) : '';
    }

    public function getPriorityText($lang = 'ru')
    {
        return DictionaryService::getWord($this->getPriorityWord(), $lang);
    }

    public function getStatusText($lang = 'ru')
    {
        return $this->status ? DictionaryService::getWord('taskStatus' . ucfirst($this->status), $lang) : '';
    }

    public function getDocuments()
    {
        return Document::find()
            ->leftJoin(['td' => TaskDocument::tableName()], 'td.document_id = documents.id')
            // ->where(['id' => 'td.task_id'])
            ->andWhere(['td.task_id' => $this->id])
            ->all();
    }

    public function getComments()
    {
        return TaskComment::find()
            ->where(['task_id' => $this->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }

    public function getActivities()
    {
        return TaskActivity::find()
            ->where(['task_id' => $this->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }
}
