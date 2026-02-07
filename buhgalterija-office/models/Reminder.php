<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reminder".
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $template_id
 * @property int|null $send_date
 * @property string|null $type
 * @property string|null $message
 * @property string|null $status
 */
class Reminder extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reminder';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'template_id', 'send_date', 'type', 'message'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['company_id', 'template_id', 'send_date'], 'default', 'value' => null],
            [['company_id', 'template_id', ], 'integer'],
            [['type', 'status'], 'string', 'max' => 32],
            [['message'], 'string', 'max' => 512],
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
            'template_id' => 'Template ID',
            'send_date' => 'Send Date',
            'type' => 'Type',
            'message' => 'Message',
            'status' => 'Status',
        ];
    }

}
