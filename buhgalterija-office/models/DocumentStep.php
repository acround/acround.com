<?php

namespace app\models;

use app\services\DictionaryService;
use Yii;

/**
 * This is the model class for table "document_step".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class DocumentStep extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_step';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 256],
            [['name'], 'unique'],
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
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getName($lang, $name = null)
    {
        if ($name === null) {
            $name = $this->name;
        }
        $name = 'docStatus' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        $translation = DictionaryService::getWord($name, $lang);
        return $translation;
    }
}
