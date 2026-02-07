<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "event".
 *
 * @property int $id
 * @property int|null $company_id
 * @property string|null $topic
 * @property string|null $details
 * @property string|null $status
 */
class Event extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'topic', 'details'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['company_id'], 'default', 'value' => null],
            [['company_id'], 'integer'],
            [['topic', 'details'], 'string', 'max' => 128],
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
            'topic' => 'Topic',
            'details' => 'Details',
            'status' => 'Status',
        ];
    }

}
