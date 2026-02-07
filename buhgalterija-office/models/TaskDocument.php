<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task_document".
 *
 * @property int $id
 * @property int $task_id
 * @property int $document_id
 *
 * @property Document $document
 * @property Task $task
 */
class TaskDocument extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_document';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'document_id'], 'required'],
            [['task_id', 'document_id'], 'default', 'value' => null],
            [['task_id', 'document_id'], 'integer'],
            [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => Document::class, 'targetAttribute' => ['document_id' => 'id']],
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
            'document_id' => 'Document ID',
        ];
    }

    /**
     * Gets query for [[Document]].
     *
     * @return Document
     */
    public function getDocument()
    {
        return Document::findOne(['id' => $this->document_id]);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return Task
     */
    public function getTask()
    {
        return Task::findOne(['id' => $this->task_id]);
    }

}
