<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "accountant_activity".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $due_date
 *
 * @property AccountantAccountantActivity[] $accountantAccountantActivities
 * @property Accountant[] $accountants
 */
class AccountantActivity extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accountant_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'due_date'], 'default', 'value' => null],
            [['due_date'], 'default', 'value' => null],
            [['due_date'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'due_date' => 'Due Date',
        ];
    }

    /**
     * Gets query for [[AccountantAccountantActivities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccountantAccountantActivities()
    {
        return $this->hasMany(AccountantAccountantActivity::class, ['accountant_activity_id' => 'id']);
    }

    /**
     * Gets query for [[Accountants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccountants()
    {
        return $this->hasMany(Accountant::class, ['id' => 'accountant_id'])->viaTable('accountant_accountant_activity', ['accountant_activity_id' => 'id']);
    }

}
