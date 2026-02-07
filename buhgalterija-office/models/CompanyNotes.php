<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company_notes".
 *
 * @property int $id
 * @property int $company_id
 * @property string|null $note
 * @property int $accountant_id
 * @property string $status
 * @property string $created_at
 *
 * @property Accountant $accountant
 * @property Company $company
 */
class CompanyNotes extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company_notes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'active'],
            [['company_id', 'accountant_id'], 'required'],
            [['company_id', 'accountant_id'], 'default', 'value' => null],
            [['company_id', 'accountant_id'], 'integer'],
            [['created_at'], 'safe'],
            [['note'], 'string', 'max' => 200],
            [['status'], 'string', 'max' => 16],
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
            'note' => 'Note',
            'accountant_id' => 'Accountant ID',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets [[Accountant]].
     *
     * @return Accountant|null
     */
    public function getAccountant()
    {
        return Accountant::find()->where(['id' => $this->accountant_id])->one();
    }

    /**
     * Gets [[Company]].
     *
     * @return Company|null
     */
    public function getCompany()
    {
        return Company::find()->where(['id' => $this->company_id])->one();
    }

}
