<?php

use yii\helpers\Html;

use common\modules\base\widgets\favicon\FaviconWidget;
use common\modules\base\helpers\JsonLDHelper;
use common\modules\base\widgets\counters\yandexmetrika\YandexMetrikaCounter;
use common\modules\base\extensions\gtm\widget\GTMWidget;

use common\modules\seo\widgets\SeoHeadWidget;

?>

<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="telegram:rhash" content="c36fdcf4f21bb7">
	<meta property="fb:pages" content="159715331368551" />
	<meta name="verify-admitad" content="4ec87ecdd7" />

	<link rel="alternate" type="application/rss+xml" title="RSS" href="https://sprut.ai/rss.xml">

	<?= Html::csrfMetaTags() ?>
	
	<?= FaviconWidget::widget([
		'web' => '@web',
		'webroot' => '@webroot',
		'favicon' => '@webroot/images/icon/favicon.png',
		'appname' => Yii::$app->name,
		'color' => '#dd9c54',
	]); ?>

	<?= SeoHeadWidget::widget() ?>

    <?php JsonLDHelper::registerScripts(); ?>
	<?php $this->head() ?>
	
	<?php if (Yii::$app->params['metrika.enabled']) { ?>
	<!-- Yandex.Metrika counter -->
	<?php
	$userParams = [];
	if (!Yii::$app->user->isGuest) {
		$user = Yii::$app->user->identity;
		$profile = $user->profile;
		$userParams['user_id'] = $user->id;
		$userParams['user_username'] = $user->username;
		$userParams['user_email'] = $user->email;
		if (strlen($profile->first_name))
			$userParams['user_first_name'] = $profile->first_name;
		if (strlen($profile->last_name))
			$userParams['user_last_name'] = $profile->last_name;
	}
	?>
	<?= YandexMetrikaCounter::widget([
		'counterId' => 48768050,
		'clickmap' => true,
		'trackLinks' => true,
		'accurateTrackBounce' => true,
		'webvisor' => false,
		//'userParams' => $userParams,
	]) ?>
	<?= YandexMetrikaCounter::widget([
		'counterId' => 51650159,
		'clickmap' => true,
		'trackLinks' => true,
		'accurateTrackBounce' => true,
		'webvisor' => false,
		//'userParams' => $userParams,
	])  ?>
	<!-- /Yandex.Metrika counter -->
	
	
	<!-- Google Tag Manager -->
	<?= GTMWidget::widget([]) ?>
	<!-- /Google Tag Manager -->
	
	<?php } ?>
</head>