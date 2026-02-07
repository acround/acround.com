<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document_activity".
 *
 * @property int $id
 * @property int $document_id
 * @property int $accountant_id
 * @property int $step_id
 * @property string|null $created_at
 */
class DocumentActivity extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'default', 'value' => null],
            [['document_id', 'accountant_id', 'step_id'], 'required'],
            [['document_id', 'accountant_id', 'step_id'], 'default', 'value' => null],
            [['document_id', 'accountant_id', 'step_id'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'document_id' => 'Document ID',
            'accountant_id' => 'Accountant ID',
            'step_id' => 'Step ID',
            'created_at' => 'Created At',
        ];
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        return parent::save($runValidation, $attributeNames);
    }

    public function getStepName($lang = 'ru')
    {
        $step = DocumentStep::findOne(['id' => $this->step_id]);
        if ($step) {
            return $step->getName($lang);
        }
        return null;
    }
}
