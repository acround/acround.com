<?php

namespace app\models;

use app\services\DictionaryService;
use Yii;

use function Symfony\Component\String\s;

/**
 * This is the model class for table "task_step".
 *
 * @property int $id
 * @property string $name
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TaskActivity[] $taskActivities
 */
class TaskStep extends \yii\db\ActiveRecord
{

    const STEP_NEW = 'created';
    const STEP_IN_PROGRESS = 'in_progress';
    const STEP_WAITING = 'waiting';
    const STEP_DONE = 'done';
    const STEP_OVERDUE = 'overdue';
    const STEP_CLOSED = 'closed';
    const STEP_ARCHIVED = 'archived';

    public static $steps = [
        'new' =>  self::STEP_NEW,
        'inProgress' =>  self::STEP_IN_PROGRESS,
        'waiting' =>  self::STEP_WAITING,
        'done' =>  self::STEP_DONE,
        'overdue' =>  self::STEP_OVERDUE,
        'closed' =>  self::STEP_CLOSED,
        'archived' =>  self::STEP_ARCHIVED,

    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_step';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[TaskActivities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskActivities()
    {
        return $this->hasMany(TaskActivity::class, ['step_id' => 'id']);
    }

    public function getName($lang)
    {
        $nameArr = explode('_', $this->name);
        foreach ($nameArr as &$part) {
            $part = ucfirst($part);
        }
        $nameKey = implode('', $nameArr);
        return DictionaryService::getWord('taskStep' . $nameKey, $lang);
    }
}
