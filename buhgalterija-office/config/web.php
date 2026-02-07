<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    // "traceLevel" => 3,
    'id' => 'buhgalterija-backoffice',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'i18n',],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    // Настройка по умолчанию: русский. Сербский как второй язык.
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',

    'components' => [
        'telegram' => [
            'class' => 'app\components\TelegramComponent',
            'botToken' => getenv('TELEGRAM_BOT_TOKEN'),
        ],

        'request' => [
            'cookieValidationKey' => '567893-946589-985497-927359',
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            // 'enableSession' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        // --- Настройка RBAC для ролей ---
        'authManager' => [
            'class' => 'yii\rbac\DbManager', // Используем базу данных (PostgreSQL)
            'db' => 'db', // По умолчанию использует компонент 'db'
            'itemTable' => 'auth_item', // Имя таблицы для элементов (ролей/разрешений)
            'itemChildTable' => 'auth_item_child', // Имя таблицы для иерархии
            'assignmentTable' => 'auth_assignment', // Имя таблицы для назначений
            'ruleTable' => 'auth_rule', // Имя таблицы для правил
        ],

        // --- Настройка Мультиязычности (i18n) ---
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'ru-RU',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],

        // URL Manager для ЧПУ (опционально)
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'GET document/<action>/<id:\d+>' => 'document/<action>',
                'GET auth' => 'site/login',
                'GET <controller:\w+>/<action:\w+>' => '<controller>/<action>',
                'GET <controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',

                'POST reminder/tax-calendar/<month:[\w\-]+>' => 'reminder/tax-calendar',
                'POST reminder/tax-calendar-table/<month:[\w\-]+>' => 'reminder/tax-calendar-table',
                'POST <controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                'POST <controller:\w+>/<action:\w+>/<status:[\w\-]+>' => '<controller>/<action>',
                'POST <controller:\w+>/<action:\[w\-]+>' => '<controller>/<action>',
                'POST login' => 'auth/login',
                'POST api/<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
