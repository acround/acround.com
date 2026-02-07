<?php

use yii\db\Migration;

class m260115_141214_accountant2company_link extends Migration
{
    private $links = [
        'Jelena Milojković' => [
            'SEAL & TEA',
            'Andrei Girko pe Bananalama',
            'Andrei Zubarev',
            'Beavers Global',
            'Creomate',
            'Egor Khorkin PR Kafa i Kolaci',
            'GREEN HOUSE COFFEE DOO NOVI SAD',
            'Ivan Edemskii pr',
            'Jezeto',
            'Konobars',
            'Mikhail Butorin pr Les',
            'Nataliia Podolskaia',
            'Nataliia Prokhorskaia pr',
            'noma Dev',
            'OnlyApps',
            'RTT Europe',
            'smartvez',
            'Svetlana Shalygina',
            'family office',
            'TM-Horeca',
            'VADIM DMITRIUKOV PR NOVI SAD',
            'Vladimir Denisultanov',
            'Yury Sorokin pr Trava',
            'ULTRAMARINE',
        ],
        'Julija Radčenko' => [
            'Aigul Akhmetzianova',
            'Aleksandr Nedopekin pe',
            'Aleksandr Safronov pr',
            'ALEKSANDR GORDIENKO PR',
            'Anton Saburov pr',
            'Dmytro Kovalenko PR',
            'Valerii Timofeev pr',
            'Vladimir Nikolaev PR Novi Sad',
            'Alm Tech',
            'Drop Table',
            'Girid Software',
            'Lorem ipsum',
            'Kidotech',
            'WAL Consulting',
        ],
        'Viktorija Egorova' => [
            'Iana Motina PR SUNRICE',
            'Nikolay Pereslavtsev PR Marnivi',
            'Maksim Grishin PR',
            'kalopsia',
            'NOVI ČAJ',
            'Kuruneko',
            'Ikigai Bubble',
            'Ikigai 021',
        ],
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach ($this->links as $accountant => $companies) {
            list($first_name, $last_name) = explode(' ', $accountant);
            $accountant_id = $this->db->createCommand("SELECT id FROM accountant WHERE firstname = '$first_name' AND lastname = '$last_name'")->queryScalar();
            if (!$accountant_id) {
                echo "Accountant $accountant not found\n";
                continue;
            }
            foreach ($companies as $company) {
                $company_id = $this->db->createCommand("SELECT id FROM company WHERE name = '$company'")->queryScalar();
                if ($company_id) {
                    $this->insert('company_accountant', ['accountant_id' => $accountant_id, 'company_id' => $company_id]);
                    // $this->db->createCommand("INSERT INTO company_accountant (accountant_id, company_id) VALUES ($accountant_id, $company_id)")->execute();
                } else {
                    echo $company . " - FAIL\n";
                }
            }
        }
        // return false;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand("TRUNCATE company_accountant RESTART IDENTITY")->execute();
    }
}
