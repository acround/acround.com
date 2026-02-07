<?php

$dbHost = getenv('POSTGRES_HOST') ?: 'localhost';
$dbPort = getenv('POSTGRES_PORT') ?: '5432';
$dbName = getenv('POSTGRES_DATA_DB') ?: 'n8n_data';
$dbUser = getenv('POSTGRES_USER') ?: 'n8n';
$dbPassword = getenv('POSTGRES_PASSWORD') ?: 'E9ekjPy9FipU';

$ret = [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=' . $dbHost . ';port=' . $dbPort . ';dbname=' . $dbName,
    'username' => $dbUser,
    'password' => $dbPassword,
    'charset' => 'utf8',
];

return $ret;
