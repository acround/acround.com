<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company_type".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property Company[] $companies
 */
class CompanyType extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'default', 'value' => null],
            [['name'], 'string', 'max' => 512],
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
        ];
    }

    /**
     * Gets query for [[Companies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::class, ['type_id' => 'id']);
    }

    public static function getIdByName($name)
    {
        $type = self::findOne(['name' => $name]);
        return $type ? $type->id : null;
    }
}
