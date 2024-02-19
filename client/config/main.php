<?php

$params = array_merge(
	require(__DIR__.'/../../common/config/params.php'), 
	require(__DIR__.'/../../common/config/params-local.php'), 
	require(__DIR__.'/params.php'),
	require(__DIR__.'/params-local.php')
);

return [
	'id' => 'client',
	'basePath' => dirname(__DIR__),
	'bootstrap' => [
		'log',
	],
	'controllerNamespace' => 'client\controllers',
	'homeUrl' => '/client',
	'defaultRoute' => 'site/index',
	'components' => [
		'i18n' => [
			'translations' => [
				'*' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@client/messages',
					'fileMap' => [
						'app' => 'app.php',
						'projects' => 'projects.php',
					],
				],
			],
		],
		'urlManager' => [
			'class' => 'common\modules\seo\components\UrlManager',
			'baseUrl' => '/client',
			'enableStrictParsing' => true,
			'rules' => [
				
				['pattern' => 'sitemap', 'route' => 'sitemap/index', 'suffix' => '.xml'],
				['pattern' => 'yandexturbo-articles', 'route' => 'yandexTurboArticles/yandex-turbo/index', 'suffix' => '.xml'],
				['pattern' => 'yandexturbo-news', 'route' => 'yandexTurboNews/yandex-turbo/index', 'suffix' => '.xml'],
				['pattern' => 'yandexturbo-blogs', 'route' => 'yandexTurboBlogs/yandex-turbo/index', 'suffix' => '.xml'],
				['pattern' => 'yandexturbo-portfolios', 'route' => 'yandexTurboPortfolios/yandex-turbo/index', 'suffix' => '.xml'],
				
				'telegram/<username:[\w+\ \-]+>' => 'site/telegram',
				
				// Menu
				//'menus/<id:\d+>/<tag:[\w+\ \-]+>' => 'menus/view',
				//'menus/tag/<id:\d+>/<tag:[\w+\ \-]+>' => 'menus/tag',
				
				// Article
				'article/<id:\d+>' => 'article/view',
				'article/<action:\w+>/<id:\d+>' => 'article/<action>',
				'article/<action:\w+>' => 'article/<action>',
				
				// News
				'news/<id:\d+>' => 'news/view',
				'news/<action:\w+>/<id:\d+>' => 'news/<action>',
				'news/<action:\w+>' => 'news/<action>',
				
				// Blog
				'blog/<id:\d+>' => 'blog/view',
				'blog/<action:\w+>/<id:\d+>' => 'blog/<action>',
				'blog/<action:\w+>' => 'blog/<action>',
				
				// Portfolio
				'portfolio/<id:\d+>' => 'portfolio/view',
				'portfolio/tag/<tag:[\w+\ \-]+>' => 'portfolio/tag',
				'portfolio/<action:\w+>/<id:\d+>' => 'portfolio/<action>',
				'portfolio/<action:\w+>' => 'portfolio/<action>',
				
				// Project
				'project/payer/<action:\w+>/<project_id:\d+>/<id:\d+>' => 'project/payer/<action>',
				'project/payer/<action:\w+>/<project_id:\d+>' => 'project/payer/<action>',
				'project/event/<action:\w+>/<project_id:\d+>/<id:\d+>' => 'project/event/<action>',
				'project/event/<action:\w+>/<project_id:\d+>' => 'project/event/<action>',
				
				'projects/question/<project_id:\d+>/<id:\d+>' => 'projects/question/view',
				'projects/question/<project_id:\d+>/<action:\w+>/<id:\d+>' => 'projects/question/<action>',
				'projects/question/<project_id:\d+>/<action:\w+>' => 'projects/question/<action>',
				
				'projects/event/<project_id:\d+>/<id:\d+>' => 'projects/default/event-view',
				
				'projects/<id:\d+>' => 'projects/default/view',
				'projects/<action:\w+>/<id:\d+>' => 'projects/default/<action>',
				'projects/<action:\w+>' => 'projects/default/<action>',
				
				// Plugins
				'plugins/<id:\d+>' => 'plugins/view',
				'plugins/<action:\w+>/<id:\d+>' => 'plugins/<action>',
				
				// Companies
				'companies/default/<id:\d+>' => 'companies/default/view',
				'companies/default/<action:\w+>/<id:\d+>' => 'companies/default/<action>',
				'companies/default/<action:\w+>' => 'companies/default/<action>',
				'companies/<controller:\w+>/<company_id:\d+>/<id:\d+>' => 'companies/<controller>/view',
				'companies/<controller:\w+>/<company_id:\d+>/<action:\w+>/<id:\d+>' => 'companies/<controller>/<action>',
				'companies/<controller:\w+>/<company_id:\d+>/<action:\w+>' => 'companies/<controller>/<action>',
				
				// Tags
				'tags/index' => 'tags/index',
				'tags/news/<title:[\w+\ \-\.]+>' => 'tags/news',
				'tags/blogs/<title:[\w+\ \-\.]+>' => 'tags/blogs',
				'tags/projects/<title:[\w+\ \-\.]+>' => 'tags/projects',
				'tags/plugins/<title:[\w+\ \-\.]+>' => 'tags/plugins',
				'tags/authors/<title:[\w+\ \-\.]+>' => 'tags/authors',
				'tags/companies/<title:[\w+\ \-\.]+>' => 'tags/companies',
				'tags/<title:[\w+\ \-\.]+>' => 'tags/view',
				
				// Contests
				'contests/<date:\d{2}-\d{4}>' => 'contests/view',
				
				// Telegram
				'telegram/default/link/<code:[\w\-]+>' => 'telegram/default/link',
				
				// Pastes
				'pastes/index' => 'pastes/index',
				'pastes/user/<user_id:\d+>' => 'pastes/user',
				'pastes/<slug:\w+>' => 'pastes/view',
				
				
				// Modules
				'<module:\w+>/<controller:[\w\-]+>/<id:\d+>/<alias:[\w\-]+>' => '<module>/<controller>/view',
				'<module:\w+>/<controller:[\w\-]+>/<id:\d+>' => '<module>/<controller>/view',
				'<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>' => '<module>/<controller>/<action>',
				'<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>/<tags:[\w\-]+>' => '<module>/<controller>/<action>',
				'<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/<controller>/<action>',
				'<module:\w+>/<controller:[\w\-]+>' => '<module>/<controller>',
				
				'<module:\w+>/<id:\d+>' => '<module>/default/view',
				'<module:\w+>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
				'<module:\w+>/<action:\w+>' => '<module>/default/<action>',
				
				// Controllers
				'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
				
				'' => 'site/index',
				
				'defaultRoute' => 'site/index',
			],
		],
		'assetManager' => [
			'linkAssets' => true,
			'appendTimestamp' => true,
		],
		
		'view' => [
			'class' => 'appmake\yii2\minify\View',
			'web_path' => '@web',
			'base_path' => '@webroot',
			'minify_path' => '@webroot/minify',
			'theme' => [
				'pathMap' => [
					'@common/modules/user/views' => '@client/views/user',
				],
			],
			'js_position' => [
				\yii\web\View::POS_BEGIN,
				\yii\web\View::POS_END,
			],
			'force_charset' => 'UTF-8',
			'compress_output' => false,
			'enableMinify' => false,
			'minifyCss' => false,
			'minifyJs' => false,
			'as seo' => [
				'class' => 'common\modules\seo\behaviors\SeoBehavior',
			],
		],
		'request' => [
            'baseUrl' => '/client',
        ],
		'errorHandler' => [
			'class' => 'bedezign\yii2\audit\components\web\ErrorHandler',
			'errorAction' => 'site/error',
		],
	],
	'modules' => [
		'user' => [
			'controllerMap' => [
				'signup' => 'client\controllers\user\SignupController',
				'security' => 'client\controllers\user\SecurityController',
				'forgot' => 'client\controllers\user\ForgotController',
				'content' => 'client\controllers\user\ContentController',
				'subscribers' => 'client\controllers\user\SubscribersController',
			],
		],
		'media' => [
			'placeholderPath' => '@client/web/images/placeholder/placeholder.jpg',
		],
		'qa' => [
			'class' => 'artkost\qa\Module',
			'userNameFormatter' => function($data) {
				return $data->username;
			},
			'controllerMap' => [
				'default' => 'client\controllers\qa\DefaultController',
			],
			'viewPath' => '@client/views/qa',
		],
		'dynagrid' => [
			'class' => '\kartik\dynagrid\Module',
		],
		'gridview' => [
			'class' => '\kartik\grid\Module',
		],
	],
	'params' => $params,
];
