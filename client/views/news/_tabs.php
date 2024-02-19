<?php

use yii\helpers\Html;
use yii\helpers\Url;

use common\modules\base\extensions\bootstrap\Nav;

/* @var $this yii\web\View */

?>

<?= Nav::widget([
	'options' => ['class' => 'nav navbar-nav tabs'],
	'activateParents' => true,
	'labelTag' => false,
	'debug' => true,
	'classNormal' => 'btn btn-lg btn-default',
	'classActive' => 'btn btn-lg btn-primary',
	'items' => [
		['label' => Yii::t('news', 'tab_newest'), 'url' => ['news/index']],
		['label' => Yii::t('news', 'tab_popular'), 'url' => ['news/popular']],
		['label' => Yii::t('news', 'tab_discussed'), 'url' => ['news/discussed']],
		['label' => Yii::t('news', 'tab_subscribed'), 'url' => ['news/subscribed']],
		['label' => Yii::t('news', 'button_add'), 'icon' => 'glyphicon glyphicon-plus', 'url' =>  ['content/news/create']],
	]
]) ?>

<div class="clearfix"></div>
