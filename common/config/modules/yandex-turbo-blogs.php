<?php

return [
	'class' => \common\modules\base\extensions\yandexturbo\YandexTurbo::class,
	'link' => 'https://sprut.ai/client',
	'description' => 'Портал умного дома',
	'cacheKey' => 'yandexTurboBlogs',
	'elements' => [
		\common\modules\content\models\Blog::class,
	],
];