<?php

use yii\web\Response;

$params = array_merge(
	require(__DIR__.'/../../common/config/params.php'),
	require(__DIR__.'/../../common/config/params-local.php'),
	require(__DIR__.'/params.php'),
	require(__DIR__.'/params-local.php')
);

return [
	'id' => 'api',
	'basePath' => dirname(__DIR__),
	'controllerNamespace' => 'api\controllers',
	'bootstrap' => [
		[
			'class' => yii\filters\ContentNegotiator::class,
			'formats' => [
				'application/json' => Response::FORMAT_JSON,
				'application/xml' => Response::FORMAT_XML,
			],
		],
		'v1',
	],
	'defaultRoute' => 'v1/default',
	'components' => [
		'user' => [
			'class' => 'common\modules\user\components\User',
			'identityClass' => 'api\models\user\User',
			'enableSession' => false
		],
		'i18n' => [
			'translations' => [
				'*' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@api/messages',
				],
			],
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'enableStrictParsing' => false,
			'showScriptName' => false,
			'rules' => [
			    
                'docs' => 'docs/docs',
			    
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/user',
                    ],
                    'patterns' => [
                        'POST signin' => 'auth/signin',
                        'POST signup' => 'auth/signup',
                        'POST logout' => 'auth/logout',
                        'POST forgot' => 'auth/forgot',
                        'GET me' => 'auth/me',
                        'POST forgot/validate/{id}/{code}' => 'auth/forgot-validate',
                        'POST forgot/complete/{id}/{code}' => 'auth/forgot-complete',
                        'GET auth/social/me' => 'auth/social-me',
                        'POST auth/social/{provider}' => 'auth/social',

                        'GET,HEAD {username}' => 'default/username',
                        'GET,HEAD {id}/activity' => 'default/activity',
                        'GET,HEAD {id}/activity/stats' => 'default/activity-stats',

                        'PUT,PATCH profile' => 'profile/update'
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{code}' => '<code:\\d+>',
                        '{provider}' => '<provider:[a-zA-Z0-9_\-]+>',
                        '{username}' => '<username:[a-zA-Z0-9\._\-]+>',
                    ],
                ],
				[
					'class' => 'yii\rest\UrlRule',
					'controller' => ['v1/content'],
                    'patterns' => [
                        'GET,HEAD' => 'default/index',
                        'GET,HEAD admin' => 'default/admin',
                        'POST' => 'default/create',
                        'GET,HEAD {id}' => 'default/view',
                        'PUT,PATCH {id}' => 'default/update',
                        'DELETE {id}' => 'default/delete',

                        'GET,HEAD counts' => 'default/counts',
                        'GET,HEAD slug/{slug}' => 'default/view-slug',
                        'GET,HEAD {id}/items' => 'default/items',

                        'GET,HEAD history' => 'history/index',
                        'POST history' => 'history/create',

                        'POST utils/youtube-info' => 'utils/youtube-info',
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{slug}' => '<slug:[a-z0-9_\-]+>',
                    ],
				],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/comment'],
                    'patterns' => [
                        'GET,HEAD {module_type}/{module_id}' => 'default/index',
                        'POST' => 'default/create',
                        'PUT,PATCH {id}' => 'default/update',
                        'DELETE {id}' => 'default/delete',
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{module_type}' => '<module_type:\\d+>',
                        '{module_id}' => '<module_id:\\d+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/vote'],
                    'patterns' => [
                        'POST like/{entityType}/{entityId}' => 'default/like',
                    ],
                    'tokens' => [
                        '{entityType}' => '<entityType:[a-zA-Z]+>',
                        '{entityId}' => '<entityId:\\d+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/tag'],
                    'patterns' => [
                        'GET,HEAD' => 'default/index',
                        'POST' => 'default/create'
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/catalog'],
                    'patterns' => [
                        'GET,HEAD items' => 'item/index',
                        'GET,HEAD items/{id}' => 'item/view',
                        'GET,HEAD items/slug/{slug}' => 'item/slug',
                        'GET,HEAD items/{id}/contents' => 'item/contents',
                        'GET,HEAD items/{id}/owners' => 'item/owners',
                        'POST items' => 'item/create',
                        'PUT,PATCH items/{id}' => 'item/update',
                        'DELETE items/{id}' => 'item/delete',

                        'GET,HEAD orders' => 'order/index',
                        'GET,HEAD orders/{id}' => 'order/view',
                        'POST orders' => 'order/create',
                        'PUT orders/{id}' => 'order/update',
                        'DELETE orders/{id}' => 'order/delete',

                        'GET orders/link/{hash}' => 'order/view-link',
                        'PUT orders/link/{hash}' => 'order/update-link',
                        'PUT orders/pay/{hash}' => 'order/pay-link',

                        'GET,HEAD fields/groups' => 'field-group/index',
                        'GET,HEAD fields/groups/{id}' => 'field-group/view',
                        'POST fields/groups' => 'field-group/create',
                        'PUT,PATCH fields/groups/{id}' => 'field-group/update',
                        'DELETE fields/groups/{id}' => 'field-group/delete',

                        'GET,HEAD fields' => 'field/index',
                        'GET,HEAD fields/{id}' => 'field/view',
                        'POST fields' => 'field/create',
                        'PUT,PATCH fields/{id}' => 'field/update',
                        'DELETE fields/{id}' => 'field/delete',
                        'POST fields/swap' => 'field/swap',

                        'items/{id}' => 'options',
                        'items/slug/{id}' => 'options',
                        'order/link/{link}' => 'options',
                        '{id}' => 'options',
                        '' => 'options'
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{slug}' => '<slug:[a-z0-9_\-]+>',
                        '{hash}' => '<hash:[a-z0-9_\-]+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/favorite'],
                    'patterns' => [
                        'GET,HEAD groups' => 'group/index',
                        'POST groups' => 'group/create',
                        'PUT,PATCH groups/{id}' => 'group/update',
                        'DELETE groups/{id}' => 'group/delete',
                        'PUT,PATCH groups/{id}/set/{module_id}' => 'group/favorite-set',
                        'DELETE groups/clear/{module_type}/{module_id}' => 'group/favorite-clear',
                        'groups' => 'options',
                        '{id}' => 'options',
                        '' => 'options'
                    ],
                    'tokens' => [
                        '{module_type}' => '<module_type:\\d+>',
                        '{module_id}' => '<module_id:\\d+>',
                        '{id}' => '<id:\\d+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/achievement'],
                    'patterns' => [
                        'GET,HEAD users' => 'user/index',
                        'GET,HEAD users/top' => 'user/top',
                        'GET,HEAD' => 'default/index',
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/telegram'],
                    'patterns' => [
                        'GET,HEAD chats' => 'chat/index',
                        'GET,HEAD chats/admin' => 'chat/admin',
                        'GET,HEAD chats/tags' => 'chat/tags',
                        'GET,HEAD chats/{id}' => 'chat/view',
                        'chats/{id}' => 'options',
                        '{id}' => 'options',
                        '' => 'options'
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/dashboard'],
                    'patterns' => [
                        'GET,HEAD content/aliexpress' => 'content/aliexpress',
                        'GET,HEAD content/not-own-image' => 'content/not-own-image',
                        'GET,HEAD catalog/stat-review' => 'catalog/stat-review',
                        'GET,HEAD catalog/stat-owner' => 'catalog/stat-owner',
                        'GET,HEAD order/index' => 'order/index',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/media'],
                    'patterns' => [
                        'POST upload/slim/{module_type}/{module_id}' => 'upload/slim',
                        'DELETE upload/slim/{module_type}/{module_id}/{id}' => 'upload/delete',
                    ],
                    'tokens' => [
                        '{module_type}' => '<module_type:\\d+>',
                        '{module_id}' => '<module_id:\\d+>',
                        '{id}' => '<id:\\d+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/cdek'],
                    'patterns' => [
                        'POST calculate' => 'calculate',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/image'],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/mailing'],
                ],
				[
					'class' => 'yii\rest\UrlRule',
					'controller' => [
						'v1/user',
					],
					'patterns' => [
						'GET profile' => 'profile-view',
						'POST profile' => 'profile-update',
						'PUT profile/password' => 'profile-password-update',
						'PUT,PATCH {id}' => 'update',
						'DELETE {id}' => 'delete',
						'GET,HEAD {id}' => 'view',
						'POST' => 'create',
						'GET,HEAD' => 'index',
						'{id}' => 'options',
						'' => 'options'
					],
					'tokens' => [
						'{id}' => '<id:\\d+>',
					],
				],
			],
		],
		'request' => [
			'enableCookieValidation' => false,
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
                'multipart/form-data' => 'yii\web\MultipartFormDataParser',
			],
		],
		'response' => [
			'class' => 'yii\web\Response',
		],
        'errorHandler' => [
            'class' => 'bedezign\yii2\audit\components\web\ErrorHandler',
        ],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => [
						'error', 'warning'
					],
				],
			],
		],
	],
	'modules' => [
		'v1' => [
			'basePath' => '@api/modules/v1',
			'class' => 'api\modules\v1\Module',
		],
		'user' => [
			'urlRules' => [],
		]
	],
	'params' => $params,
];
        