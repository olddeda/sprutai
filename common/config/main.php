<?php

if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];

return [
	'id' => 'common',
	'name' => 'Sprut.ai',
	'version' => '1.0.0',
	'vendorPath' => dirname(dirname(__DIR__)).'/vendor',
	
	'language' => 'ru-RU',
	'sourceLanguage' => 'en-EN',
	
	'bootstrap' => [
		'common\modules\base\Bootstrap',
		'common\modules\user\Bootstrap',
		'common\modules\rbac\Bootstrap',
		'common\modules\notification\Bootstrap',
		'common\modules\media\Bootstrap',
		'common\modules\lookup\Bootstrap',
		'common\modules\menu\Bootstrap',
		'common\modules\dashboard\Bootstrap',
		'common\modules\seo\Bootstrap',
		'common\modules\tag\Bootstrap',
		'common\modules\payment\Bootstrap',
		'common\modules\content\Bootstrap',
        'common\modules\project\Bootstrap',
		'common\modules\plugin\Bootstrap',
		'common\modules\comments\Bootstrap',
		'common\modules\vote\Bootstrap',
		'common\modules\statistics\Bootstrap',
		'common\modules\settings\Bootstrap',
		'common\modules\social\Bootstrap',
		'common\modules\backup\Bootstrap',
		'common\modules\telegram\Bootstrap',
		'common\modules\banner\Bootstrap',
		'common\modules\contest\Bootstrap',
		'common\modules\event\Bootstrap',
		'common\modules\queues\Bootstrap',
		'common\modules\paste\Bootstrap',
		'common\modules\company\Bootstrap',
		'common\modules\catalog\Bootstrap',
        'common\modules\shortener\Bootstrap',
        'common\modules\mailing\Bootstrap',
        'common\modules\favorite\Bootstrap',
        'common\modules\achievement\Bootstrap',
        'common\modules\hub\Bootstrap',
        'common\modules\cdek\Bootstrap',
        'common\modules\qa\Bootstrap',
		
		'maintenance',
		'queue',
		'log',
		
		'client',
	],
	'aliases' => [
		'@root' => realpath(__DIR__.'/../../'),
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
	],
	'controllerMap' => [
		'migrate' => [
			'class' => 'yii\console\controllers\MigrateController',
			'migrationPath' => null,
			'migrationNamespaces' => [
				'yii\queue\db\migrations',
			],
		],
	],
	'components' => [
		'user' => [
			'class' => 'common\modules\user\components\User',
		],
		'session' => [
			'class' => 'yii\redis\Session',
			'timeout' => 31536000, // 1 year
		],
		'i18n' => [
			'translations' => [
				'*' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en-EN',
					'basePath' => '@common/messages',
				],
			],
		],
		'formatter' => [
			'class' => 'common\modules\base\components\Formatter',
            'currencyCode' => 'RUB',
			//'thousandSeparator' => ' ',
			'numberFormatterOptions' => [
				NumberFormatter::FRACTION_DIGITS => 0,
			],
            'sizeFormatBase' => 1024,
		],
		'authManager' => [
			'class' => 'common\modules\rbac\components\DbManager',
			'db' => 'db',
			'defaultRoles' => ['guest', 'user'],
			'cache' => 'cacheFile',
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
				//[
				//	'class' => 'common\modules\telegram\components\TelegramTarget',
				//	'levels' => ['error'],
				//	'chatId' => '357615556',
				//],
			],
		],
		'settings' => [
			'class' => 'common\modules\settings\components\Settings',
		],
		'notification' => [
			'class' => 'common\modules\notification\components\Notification',
		],
		'statistics' => [
			'class' => 'common\modules\statistics\components\StatisticsComponent',
			'driver' => 'mysql',
		],
		'maintenance' => [
			'class' => 'common\modules\maintenance\Maintenance',
			'layoutPath' => '@client/views/layout/main_single',
			'viewPath' => '@client/views/maintenance/index',
		],
		'httpclient' => [
			'class' =>'common\modules\base\components\httpclient\Client',
			'detectMimeType' => true,
		],
        'geoip' => [
            'class' => 'lysenkobv\GeoIP\GeoIP'
        ],
	],
	'modules' => [
		'client' => [
			'class' => 'client\Module',
		],
		
		'base' => [
			'class' => 'common\modules\base\Module',
		],
		'user' => [
			'class' => 'common\modules\user\Module',
			'admins' => ['sprut.ai', 'deda', 'sprut'],
			'mailer' => [
				'sender' => ['noreply@sprut.ai' => 'Sprut.ai'],
			],
		],
		'notification' => [
			'class' => 'common\modules\notification\Module',
			'providers' => [
				
				// E-mail
				'email' => [
					'class' => 'common\modules\notification\providers\EmailProvider',
					'events' => [
						'common\modules\notification\components\Notification' => [
							'Email',
						],
					],
				],
				
				// Telegram
				'telegram' => [
					'class' => 'common\modules\notification\providers\TelegramProvider',
					'events' => [
						'common\modules\notification\components\Notification' => [
							'Telegram',
						],
					]
				],
			],
		],
		'rbac' => [
			'class' => 'common\modules\rbac\Module',
		],
		'media' => [
			'class' => 'common\modules\media\Module',
		],
		'lookup' => [
			'class' => 'common\modules\lookup\Module',
		],
		'menu' => [
			'class' => 'common\modules\menu\Module',
		],
		'dashboard' => [
			'class' => 'common\modules\dashboard\Module',
			'widgets' => [
				'common\modules\user\widgets\dashboard\CounterUsersDashboardWidget',
				'common\modules\content\widgets\dashboard\CounterArticlesDashboardWidget',
				'common\modules\content\widgets\dashboard\CounterNewsDashboardWidget',
				'common\modules\content\widgets\dashboard\CounterProjectsDashboardWidget',
				'common\modules\payment\widgets\dashboard\CounterPaymentsTotalDashboardWidget',
				'common\modules\payment\widgets\dashboard\CounterPaymentsArticlesDashboardWidget',
				'common\modules\payment\widgets\dashboard\CounterPaymentsNewsDashboardWidget',
				'common\modules\payment\widgets\dashboard\CounterPaymentsProjectsDashboardWidget',
				'common\modules\user\widgets\dashboard\SignupUsersDashboardWidget',
				'common\modules\content\widgets\dashboard\NewContentDashboardWidget',
				'common\modules\payment\widgets\dashboard\NewPaymentsDashboardWidget',
			],
		],
		'seo' => [
			'class' => 'common\modules\seo\Module',
		],
		'tag' => [
			'class' => 'common\modules\tag\Module',
		],
		'payment' => [
			'class' => 'common\modules\payment\Module',
		],
		'content' => [
			'class' => 'common\modules\content\Module',
		],
        'project' => [
            'class' => 'common\modules\project\Module',
        ],
		'plugin' => [
			'class' => 'common\modules\plugin\Module',
		],
		'comments' => [
			'class' => 'common\modules\comments\Module'
		],
		'vote' => [
			'class' => 'common\modules\vote\Module',
			'entities' => [
				\common\modules\vote\models\Vote::USER_FAVORITE => [
					'modelName' => 'common\modules\user\models\User',
					'entityAuthorAttribute' => 'id',
					'type' => common\modules\vote\Module::TYPE_TOGGLE,
				],
				\common\modules\vote\models\Vote::COMPANY_FAVORITE => [
					'modelName' => 'common\modules\company\models\Company',
					'entityAuthorAttribute' => 'created_by',
					'type' => common\modules\vote\Module::TYPE_TOGGLE,
					'allowSelfVote' => true,
				],
				\common\modules\vote\models\Vote::CONTENT_VOTE => [
					'modelName' => 'common\modules\content\models\Content',
					'entityAuthorAttribute' => 'author_id',
				],
				\common\modules\vote\models\Vote::CONTENT_FAVORITE => [
					'modelName' => 'common\modules\content\models\Content',
					'entityAuthorAttribute' => 'author_id',
					'type' => common\modules\vote\Module::TYPE_TOGGLE,
					'allowSelfVote' => true,
				],
				\common\modules\vote\models\Vote::COMMENT_VOTE => [
					'modelName' => 'common\modules\comments\models\Comment',
					'entityAuthorAttribute' => 'created_by',
				],
                \common\modules\vote\models\Vote::COMMENT_RATING => [
                    'modelName' => 'common\modules\comments\models\Comment',
                    'entityAuthorAttribute' => 'created_by',
                    'type' => common\modules\vote\Module::TYPE_RATING,
                ],
				\common\modules\vote\models\Vote::TAG_FAVORITE => [
					'modelName' => 'common\modules\tag\models\Tag',
					'entityAuthorAttribute' => 'created_by',
					'type' => common\modules\vote\Module::TYPE_TOGGLE,
					'allowSelfVote' => true,
				],
				\common\modules\vote\models\Vote::CONTEST_VOTE => [
					'modelName' => 'common\modules\content\models\Content',
					'entityAuthorAttribute' => 'created_by',
				],
                \common\modules\vote\models\Vote::CONTEST_VOTE => [
                    'modelName' => 'common\modules\content\models\Content',
                    'entityAuthorAttribute' => 'created_by',
                ],

                \common\modules\vote\models\Vote::CATALOG_ITEM_VOTE => [
                    'modelName' => 'common\modules\catalog\models\CatalogItem',
                    'entityAuthorAttribute' => 'created_by',
                ],
				
				// To remove
				\common\modules\vote\models\Vote::ARTICLE_VOTE => [
					'modelName' => 'common\modules\content\models\Article',
					'entityAuthorAttribute' => 'author_id',
				],
				\common\modules\vote\models\Vote::ARTICLE_FAVORITE => [
					'modelName' => 'common\modules\content\models\Article',
					'entityAuthorAttribute' => 'author_id',
					'type' => common\modules\vote\Module::TYPE_TOGGLE,
					'allowSelfVote' => true,
				],
				\common\modules\vote\models\Vote::NEWS_VOTE => [
					'modelName' => 'common\modules\content\models\News',
					'entityAuthorAttribute' => 'author_id',
				],
				\common\modules\vote\models\Vote::NEWS_FAVORITE => [
					'modelName' => 'common\modules\content\models\News',
					'entityAuthorAttribute' => 'author_id',
					'type' => common\modules\vote\Module::TYPE_TOGGLE,
					'allowSelfVote' => true,
				],
				\common\modules\vote\models\Vote::PROJECT_VOTE => [
					'modelName' => 'common\modules\project\models\Project',
					'entityAuthorAttribute' => 'author_id',
				],
				\common\modules\vote\models\Vote::PROJECT_FAVORITE => [
					'modelName' => 'common\modules\project\models\Project',
					'entityAuthorAttribute' => 'author_id',
					'type' => common\modules\vote\Module::TYPE_TOGGLE,
					'allowSelfVote' => true,
				],
				\common\modules\vote\models\Vote::BLOG_VOTE => [
					'modelName' => 'common\modules\content\models\Blog',
					'entityAuthorAttribute' => 'author_id',
				],
				\common\modules\vote\models\Vote::BLOG_FAVORITE => [
					'modelName' => 'common\modules\content\models\Blog',
					'entityAuthorAttribute' => 'author_id',
					'type' => common\modules\vote\Module::TYPE_TOGGLE,
					'allowSelfVote' => true,
				],
				\common\modules\vote\models\Vote::PLUGIN_VOTE => [
					'modelName' => 'common\modules\plugin\models\Plugin',
					'entityAuthorAttribute' => 'author_id',
				],
				\common\modules\vote\models\Vote::PLUGIN_FAVORITE => [
					'modelName' => 'common\modules\plugin\models\Plugin',
					'entityAuthorAttribute' => 'author_id',
					'type' => common\modules\vote\Module::TYPE_TOGGLE,
					'allowSelfVote' => true,
				],
			],
		],
		'banner' => [
			'class' => 'common\modules\banner\Module',
		],
		'contest' => [
			'class' => 'common\modules\contest\Module',
		],
		'event' => [
			'class' => 'common\modules\event\Module',
		],
		'statistics' => [
			'class' => 'common\modules\statistics\Module',
		],
		'settings' => [
			'class' => 'common\modules\settings\Module',
		],
		'social' => [
			'class' => 'common\modules\social\Module',
		],
		'paste' => [
			'class' => 'common\modules\paste\Module',
		],
		'company' => [
			'class' => 'common\modules\company\Module',
		],
		'catalog' => [
			'class' => 'common\modules\catalog\Module',
		],
        'shortener' => [
            'class' => 'common\modules\shortener\Module',
        ],
        'mailing' => [
            'class' => 'common\modules\mailing\Module',
        ],
        'favorite' => [
            'class' => 'common\modules\favorite\Module',
        ],
        'achievement' => [
            'class' => 'common\modules\achievement\Module',
        ],
        'hub' => [
            'class' => 'common\modules\hub\Module',
        ],
        'qa' => [
            'class' => 'common\modules\qa\Module',
        ],
        'cdek' => [
            'class' => 'common\modules\cdek\Module',
        ],
		'queues' => [
			'class' => 'common\modules\queues\Module',
			'jobs' => [
				[
					'class' => 'common\modules\project\jobs\StickJob',
					'name' => 'Рассылка стиков',
				]
			],
		],
		'yandexTurboArticles' => require_once 'modules/yandex-turbo-articles.php',
		'yandexTurboNews' => require_once 'modules/yandex-turbo-news.php',
		'yandexTurboBlogs' => require_once 'modules/yandex-turbo-blogs.php',
		'yandexTurboPortfolios' => require_once 'modules/yandex-turbo-portfolios.php',
		
		'log' => [
			'class' => 'common\modules\log\Module',
			'aliases' => [
				'Frontend Errors' => '@frontend/runtime/logs/app.log',
				'Backend Errors' => '@backend/runtime/logs/app.log',
				'Console Errors' => '@console/runtime/logs/app.log',
			],
		],
	],
];
