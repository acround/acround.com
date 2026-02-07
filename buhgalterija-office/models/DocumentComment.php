<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document_comment".
 *
 * @property int $id
 * @property int $document_id
 * @property int $accountant_id
 * @property string|null $text
 * @property string|null $created_at
 */
class DocumentComment extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text', 'created_at',], 'default', 'value' => null],
            [['document_id', 'accountant_id'], 'required'],
            [['document_id', 'accountant_id'], 'default', 'value' => null],
            [['document_id', 'accountant_id'], 'integer'],
            [['created_at',], 'safe'],
            [['text'], 'string', 'max' => 256],
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
            'text' => 'Text',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets [[Accountant]].
     *
     * @return Accountant
     */
    public function getAccountant()
    {
        return Accountant::findOne($this->accountant_id);
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        return parent::save($runValidation, $attributeNames);
    }
}
