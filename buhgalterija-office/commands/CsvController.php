<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

class CsvController extends Controller
{

    public function actionIndex($a)
    {
        $out = [];
        $num = 0;
        $fields = [];
        if (($handle = fopen($a, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($num === 0) {
                    $fields = $data;
                } else {
                    $row = [];
                    for ($i = 0; $i < count($data); $i++) {
                        if (array_key_exists($i, $fields)) {
                            $row[$fields[$i]] = $data[$i];
                        }
                    }
                    $out[] = $row;
                }
                $num++;
            }

            fclose($handle);
        }
        echo json_encode($out, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
        return ExitCode::OK;
    }
}
