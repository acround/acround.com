<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\services\AuthService;
use app\models\Document;

class DocTestController extends Controller
{
    public function actionIndex()
    {
        $statuses = [
            'uploaded',
            'needsRevision',
            'checked',
        ];
        $dirPath = dirname(__DIR__) . '/test/';
        $dir = opendir($dirPath);
        $i = 0;
        while (($testFileName = readdir($dir)) !== false) {
            if (substr($testFileName, 0, 1) == '.') {
                continue;
            }
            $filePath = dirname(__DIR__) . '/test/' . $testFileName;
            $status = $statuses[$i];
            $docTest = [
                'company_id' => 86,
                'tg_id' => 280362924,
                'metadata' => '{}',
                'status' => $status,
                'filename' => $testFileName,
                'mimetype' => mime_content_type($filePath),
                'content' => file_get_contents($filePath),
            ];
            $docs = (new Document($docTest));
            $docs->save();
            echo "Document with ID {$docs->id} has been inserted\n\n";
            $i++;
            if ($i > count($statuses) - 1) {
                $i = 0;
            }
        }
        return ExitCode::OK;
    }

    public function actionClear()
    {
        Document::deleteAll(['company_id' => 86, 'tg_id' => 280362924]);
        echo "Test documents cleared\n\n";
        return ExitCode::OK;
    }
}
