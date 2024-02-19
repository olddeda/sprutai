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
		['label' => Yii::t('portfolio', 'tab_newest'), 'url' => ['portfolio/index']],
		['label' => Yii::t('portfolio', 'tab_popular'), 'url' => ['portfolio/popular']],
		['label' => Yii::t('portfolio', 'tab_discussed'), 'url' => ['portfolio/discussed']],
	]
]) ?>

<div class="clearfix"></div>
