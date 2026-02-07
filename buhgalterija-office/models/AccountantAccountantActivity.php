<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "accountant_accountant_activity".
 *
 * @property int $id
 * @property int|null $accountant_id
 * @property int|null $accountant_activity_id
 *
 * @property Accountant $accountant
 * @property AccountantActivity $accountantActivity
 */
class AccountantAccountantActivity extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accountant_accountant_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['accountant_id', 'accountant_activity_id'], 'default', 'value' => null],
            [['accountant_id', 'accountant_activity_id'], 'default', 'value' => null],
            [['accountant_id', 'accountant_activity_id'], 'integer'],
            [['accountant_id', 'accountant_activity_id'], 'unique', 'targetAttribute' => ['accountant_id', 'accountant_activity_id']],
            [['accountant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accountant::class, 'targetAttribute' => ['accountant_id' => 'id']],
            [['accountant_activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => AccountantActivity::class, 'targetAttribute' => ['accountant_activity_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'accountant_id' => 'Accountant ID',
            'accountant_activity_id' => 'Accountant Activity ID',
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
     * Gets query for [[AccountantActivity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccountantActivity()
    {
        return $this->hasOne(AccountantActivity::class, ['id' => 'accountant_activity_id']);
    }

}
