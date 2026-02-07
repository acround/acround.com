<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\TaxCalendar;

class PcController extends Controller
{

    const TEST_DIRECTORY = '@app/test/';
    const CALENDAR_DATA_FILE_NAME = 'poreski_kalendar.json';


    public function actionIndex()
    {
        $calendarFile = self::TEST_DIRECTORY . self::CALENDAR_DATA_FILE_NAME;
        $calendar = file_get_contents(\Yii::getAlias($calendarFile));
        foreach (json_decode($calendar, true) as $item) {
            $calendarItem = new TaxCalendar();
            $calendarItem->attributes = $item;
            $calendarItem->save();
            echo $item['activity_type'] . PHP_EOL;
        }
        return ExitCode::OK;
    }
}
