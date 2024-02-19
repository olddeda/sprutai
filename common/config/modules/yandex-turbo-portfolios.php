<?php

return [
	'class' => \common\modules\base\extensions\yandexturbo\YandexTurbo::class,
	'link' => 'https://sprut.ai/client',
	'description' => 'Портал умного дома',
	'cacheKey' => 'yandexTurboPortfolios',
	'elements' => [
		\common\modules\content\models\Portfolio::class,
	],
];