<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "passed_by".
 *
 * @property int $id
 * @property int|null $tg_id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $username
 * @property string|null $status
 * @property string $lang
 * @property string $created_at
 * @property string $updated_at
 */
class PassedBy extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'passed_by';
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
            [['tg_id'], 'default', 'value' => null],
            [['tg_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['firstname', 'lastname', 'username', 'status'], 'string', 'max' => 32],
            [['lang'], 'string', 'max' => 8],
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
            'status' => 'Status',
            'lang' => 'Lang',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
