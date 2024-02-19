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
		['label' => Yii::t('article', 'tab_newest'), 'url' => ['article/index']],
		['label' => Yii::t('article', 'tab_popular'), 'url' => ['article/popular']],
		['label' => Yii::t('article', 'tab_discussed'), 'url' => ['article/discussed']],
		['label' => Yii::t('article', 'tab_subscribed'), 'url' => ['article/subscribed']],
		['label' => Yii::t('article', 'button_add'), 'icon' => 'glyphicon glyphicon-plus', 'url' =>  ['content/article/create']],
	]
]) ?>

<div class="clearfix"></div>
