<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task_activity".
 *
 * @property int $id
 * @property int $task_id
 * @property int $accountant_id
 * @property int $step_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Accountant $accountant
 * @property TaskStep $step
 * @property Task $task
 */
class TaskActivity extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'accountant_id', 'step_id'], 'required'],
            [['task_id', 'accountant_id', 'step_id'], 'default', 'value' => null],
            [['task_id', 'accountant_id', 'step_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['accountant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accountant::class, 'targetAttribute' => ['accountant_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
            [['step_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskStep::class, 'targetAttribute' => ['step_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'accountant_id' => 'Accountant ID',
            'step_id' => 'Step ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Accountant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccountant()
    {
        return $this->hasOne(Accountant::class, ['id' => 'accountant_id']);
    }

    /**
     * Gets query for [[Step]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStepQuery()
    {
        return $this->hasOne(TaskStep::class, ['id' => 'step_id']);
    }

    /**
     * Gets [[Step]].
     *
     * @return TaskStep
     */
    public function getStep()
    {
        return TaskStep::findOne(['id' => $this->step_id]);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskQuery()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * Gets [[Task]].
     *
     * @return Task
     */
    public function getTask()
    {
        return Task::findOne(['id' => $this->task_id]);
    }

}
