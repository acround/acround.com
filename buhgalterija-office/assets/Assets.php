<?php
namespace app\assets;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/css/index.css',
    ];
    public $js = [
        '/js/index.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        // 'yii\bootstrap5\BootstrapAsset'
    ];
}
