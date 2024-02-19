<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
	//require(__DIR__ . '/../../api/config/params.php'),
	//require(__DIR__ . '/../../api/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
		'log',
		'api\modules\v1\Bootstrap',
	],
    'controllerNamespace' => 'console\controllers',
    'components' => [
		'urlManager' => [
			'hostInfo' => 'https://sprut.ai',
			'baseUrl' => '',
		],
        'errorHandler' => [
            //'class' => 'bedezign\yii2\audit\components\console\ErrorHandler',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
		'user' => [
			'class' => 'common\modules\user\components\User',
			'enableSession' => false,
		],
		'maintenance' => [
			'class' => 'common\modules\maintenance\Maintenance',
			'enabled' => false,
		],
    ],
	'modules' => [
		'v1' => [
			'basePath' => '@api/modules/v1',
			'class' => 'api\modules\v1\Module',
		],
	],
    'params' => $params,
];
