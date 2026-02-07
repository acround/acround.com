<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property int $id
 * @property int|null $tg_id
 * @property int|null $company_id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $username
 * @property string|null $status
 */
class Customer extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tg_id', 'company_id', 'firstname', 'lastname', 'username'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['tg_id', 'company_id'], 'default', 'value' => null],
            [['tg_id', 'company_id'], 'integer'],
            [['firstname', 'lastname', 'username', 'status'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tg_id' => 'Tg ID',
            'company_id' => 'Company ID',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'username' => 'Username',
            'status' => 'Status',
        ];
    }

}
