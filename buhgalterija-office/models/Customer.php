<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property int $id
 * @property int|null $tg_id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $username
 * @property string|null $lang
 * @property string|null $status
 */
class Customer extends \yii\db\ActiveRecord
{

    const CUSTOMER_STATUS_NEW = 'new';
    const CUSTOMER_STATUS_ACTIVE = 'active';
    const CUSTOMER_STATUS_INACTIVE = 'inactive';

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
            [['tg_id', 'firstname', 'lastname', 'username'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['lang'], 'default', 'value' => 'ru'],
            [['tg_id',], 'default', 'value' => null],
            [['tg_id',], 'integer'],
            [['firstname', 'lastname', 'status'], 'string', 'max' => 32],
            [['username'], 'string', 'max' => 128],
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
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'username' => 'Username',
            'lang' => 'Language',
            'status' => 'Status',
        ];
    }
}
