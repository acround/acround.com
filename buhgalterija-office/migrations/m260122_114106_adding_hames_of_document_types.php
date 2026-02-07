<?php

use app\models\DocumentType;
use yii\db\Migration;

class m260122_114106_adding_hames_of_document_types extends Migration
{
    private $names = [
        'unknown' => [
            'ru' => 'неизвестный',
            'rs' => 'nepoznato',
        ],
        'invoice' => [
            'ru' => 'счет-фактура',
            'rs' => 'faktura',
        ],
        'bill' => [
            'ru' => 'квитанция',
            'rs' => 'racun',
        ],
        'bankStatement' => [
            'ru' => 'выписка из банка',
            'rs' => 'izvod',
        ],
        'payroll' => [
            'ru' => 'заработная плата',
            'rs' => 'platni spisak',
        ],
        'contract' => [
            'ru' => 'договор',
            'rs' => 'ugovor',
        ],
        'taxReturn' => [
            'ru' => 'налоговая декларация',
            'rs' => 'poreska prijava',
        ],
        'payment slip' => [
            'ru' => 'платежный чек',
            'rs' => 'uplatnica',
        ],
        'other' => [
            'ru' => 'другое',
            'rs' => 'ostalo',
        ],
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(DocumentType::tableName(), 'name_ru', $this->string(32));
        $this->addColumn(DocumentType::tableName(), 'name_rs', $this->string(32));
        $this->insert(DocumentType::tableName(), ['name' => 'bill'],);
        $this->insert(DocumentType::tableName(), ['name' => 'payment slip']);
        foreach ($this->names as $key => $item) {
            $this->update(
                DocumentType::tableName(),
                ['name_ru' => $item['ru'], 'name_rs' => $item['rs']],
                ['name' => $key]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(DocumentType::tableName(), 'name_ru');
        $this->dropColumn(DocumentType::tableName(), 'name_rs');
    }
}
