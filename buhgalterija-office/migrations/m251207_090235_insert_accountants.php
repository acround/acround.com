<?php

use yii\db\Migration;
use app\services\AuthService;

class m251207_090235_insert_accountants extends Migration
{
    private $accountants = [
        [
            'id' => 0,
            'firstname' => 'Bot',
            'lastname' => 'Buhgalterija',
            'rule' => 'bot',
            'lang' => 'ru',
            'email' => '',
            'password' => '',
            'token' => '',
        ],
        [
            'id' => 0,
            'firstname' => 'Marija',
            'lastname' => 'Kovačević',
            'rule' => 'accountant',
            'lang' => 'ru',
            'email' => 'marija_kovacevic@buhgalterija.rs',
            'password' => '3P*oysc2',
            'token' => '',
        ],
        [
            'id' => 0,
            'firstname' => 'Jelena',
            'lastname' => 'Milojković',
            'rule' => 'accountant',
            'lang' => 'rs',
            'email' => 'jelena_milojkovic@buhgalterija.rs',
            'password' => '$2iXbvQC',
            'token' => '',
        ],
        [
            'id' => 0,
            'firstname' => 'Viktorija',
            'lastname' => 'Egorova',
            'rule' => 'accountant',
            'lang' => 'ru',
            'email' => 'viktorija_egorova@buhgalterija.rs',
            'password' => 'o%Mx6eh6',
            'token' => '',
        ],
        [
            'id' => 0,
            'firstname' => 'Julija',
            'lastname' => 'Radčenko',
            'rule' => 'accountant',
            'lang' => 'ru',
            'email' => 'julija_radcenko@buhgalterija.rs',
            'password' => 'vS4@roKe',
            'token' => '',
        ],
        [
            'id' => 0,
            'firstname' => 'Oleg',
            'lastname' => 'Sabančiev',
            'rule' => 'accountant',
            'lang' => 'ru',
            'email' => 'oleg_sabanciev@buhgalterija.rs',
            'password' => 'r%!82gF6',
            'token' => '',
        ],
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $renameQuery = 'ALTER TABLE "accountant" RENAME COLUMN "status" TO "rule"';
        $this->execute($renameQuery);
        $nextIdQuery = 'SELECT nextval(\'accountant_id_seq\'::regclass)';
        foreach ($this->accountants as $key => $accountant) {
            if ($key == 0) {
                $nextId = $accountant['id'];
            } else {
                $nextId = Yii::$app->db
                    ->createCommand($nextIdQuery)
                    ->queryScalar();
            }
            $accountant['id'] = $nextId;
            $accountant['password'] = AuthService::encodePassword($accountant['password']);
            $this->insert('{{%accountant}}', $accountant);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('TRUNCATE TABLE accountant RESTART IDENTITY CASCADE');
        $this->execute("ALTER SEQUENCE accountant_id_seq RESTART WITH 1");
        $renameQuery = 'ALTER TABLE "accountant" RENAME COLUMN "rule" TO "status"';
        $this->execute($renameQuery);
    }
}
