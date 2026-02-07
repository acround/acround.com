<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company_accountant".
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $accountant_id
 *
 * @property Accountant $accountant
 * @property Company $company
 */
class CompanyAccountant extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company_accountant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'accountant_id'], 'default', 'value' => null],
            [['company_id', 'accountant_id'], 'default', 'value' => null],
            [['company_id', 'accountant_id'], 'integer'],
            [['company_id', 'accountant_id'], 'unique', 'targetAttribute' => ['company_id', 'accountant_id']],
            [['accountant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accountant::class, 'targetAttribute' => ['accountant_id' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
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
            'accountant_id' => 'Accountant ID',
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
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

}
