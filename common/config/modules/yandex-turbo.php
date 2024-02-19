<?php

return [
	'class' => \common\modules\base\extensions\yandexturbo\YandexTurbo::class,
	'link' => 'https://sprut.ai/client',
	'description' => 'Портал умного дома',
	'elements' => [
		\common\modules\content\models\Article::class,
		\common\modules\content\models\News::class,
		\common\modules\content\models\Blog::class,
	],
];