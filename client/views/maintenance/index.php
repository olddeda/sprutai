<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->context->layout = 'main_single';
$this->context->layoutContent = 'content_clear';

$this->title = Yii::t('app', 'maintenance_title');

$this->context->bodyClass = 'maintenance';
?>

<div class="abs-center">
	<div class="text-center">
		<img src="/client/images/svg/logo.svg" alt="Sprut.ai" class="img-responsive">
	</div>
	<div class="text-center mb-xl">
		<div class="text-lg mb-lg"><?= $this->title ?></div>
		<p class="lead m0"><?= nl2br(Html::encode(Yii::t('app', 'maintenance_message'))) ?></p>
	</div>
	<div class="p-lg text-center">
		<span>&copy;</span>
		<span>2018 - <?= date('Y') ?></span>
		<span><br/></span>
		<span><?= Yii::$app->name ?></span>
	</div>
</div>