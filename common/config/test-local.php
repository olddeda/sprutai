<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            //'dsn' => 'mysql:host=rc1c-1idmrsjkdklxgd7u.mdb.yandexcloud.net;dbname=dev_db',
            //'dsn' => 'mysql:host=rc1c-1idmrsjkdklxgd7u.mdb.yandexcloud.net;dbname=sprut_db',
            'dsn' => 'mysql:host=localhost;dbname=dev_sprut',
            'username' => 'sprut',
            'password' => 'P3h1W1j8',
            'charset' => 'utf8mb4',
            'tablePrefix' => 'am_',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 3600,
            'on afterOpen' => function($event) {
                $event->sender->createCommand("SET sql_mode='';")->execute();
            },
            'attributes' => array(
                //PDO::MYSQL_ATTR_SSL_CA => '/var/www/app/.mysql/root.crt',
            ),
        ],
    ],
];