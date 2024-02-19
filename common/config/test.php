<?php
return [
	'id' => 'app-common-tests',
	'basePath' => dirname(__DIR__),
	'components' => [
		'user' => [
			'class' => 'common\modules\user\components\User',
			'identityClass' => 'common\modules\user\models\User',
		],
	],
];