<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task_comment".
 *
 * @property int $id
 * @property int|null $task_id
 * @property int $accountant_id
 * @property string|null $text
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Accountant $accountant
 * @property Task $task
 */
class TaskComment extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'text'], 'default', 'value' => null],
            [['task_id', 'accountant_id'], 'default', 'value' => null],
            [['task_id', 'accountant_id'], 'integer'],
            [['accountant_id'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['text'], 'string', 'max' => 256],
            [['accountant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accountant::class, 'targetAttribute' => ['accountant_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
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
            'text' => 'Text',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Accountant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccountantQuery()
    {
        return $this->hasOne(Accountant::class, ['id' => 'accountant_id']);
    }

    /**
     * Gets [[Accountant]].
     *
     * @return Accountant
     */
    public function getAccountant()
    {
        return Accountant::findOne($this->accountant_id);
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
        return Task::findOne($this->accountant_id);
    }

}
