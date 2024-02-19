<?php

return [
	'app' => [
		'basePath' => '@webroot',
		'baseUrl' => '@web',
		'css' => [
			'sass/bootstrap.scss',
			'sass/app.scss',
		],
		'js' => [
			
		],
		'depends' => [
			'yii\web\YiiAsset',
			'backend\assets\FontAwesomeAsset',
			'backend\assets\SimpleLineIconsAsset',
		],
	],
];