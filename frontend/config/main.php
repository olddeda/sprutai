<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'frontend',
    'basePath' => dirname(__DIR__),
	
    'bootstrap' => [
		'log',
	],
	
    'controllerNamespace' => 'frontend\controllers',
	
	'homeUrl' => '/',
	'defaultRoute' => 'site/index',
	
    'components' => [
		'i18n' => [
			'translations' => [
				'*' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en-EN',
					'basePath' => '@frontend/messages',
				],
			],
		],
		'urlManager' => [
			'class' => 'common\modules\seo\components\UrlManager',
			'baseUrl' => '/',
			'rules' => [
                's/<hash:\w+>' => 'shortener/default/redirect',
			    
				'client/article/<id:\d+>' => 'client/article/view',
				'client/news/<id:\d+>' => 'client/news/view',
				
				['pattern' => '<id:rss>', 'route' => 'site/rss', 'suffix' => '.xml'],
 			],
		],
		'request' => [
			'baseUrl' => '',
		],
		//'view' => [
		//	'as seo' => [
		//		'class' => 'common\modules\seo\behaviors\SeoBehavior',
		//	]
		//],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
	
	'modules' => [
		'media' => [
			'placeholderPath' => '@frontend/web/images/placeholder/placeholder.jpg',
		],
	],
	
    'params' => $params,
];
