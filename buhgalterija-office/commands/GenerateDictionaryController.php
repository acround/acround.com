<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use app\services\CalendarService;

class GenerateDictionaryController extends Controller
{

    const TEST_DIRECTORY = '@app/test/';
    const WORK_DIRECTORY = '@app/web/js/';
    const DICTIONARY_FILE_NAME = 'dictionary.js';


    public function actionIndex($type = 'prod')
    {
        $dictionary = \app\services\DictionaryService::getDictionaryJsFile(true);
        if ($type == 'prod') {
            $dictionaryFile = self::WORK_DIRECTORY . self::DICTIONARY_FILE_NAME;
        } else {
            $dictionaryFile = self::TEST_DIRECTORY . self::DICTIONARY_FILE_NAME;
        }
        file_put_contents(\Yii::getAlias($dictionaryFile), $dictionary);
        $this->stdout("Dictionary file generated successfully.\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
}
