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
		['label' => Yii::t('blog', 'tab_newest'), 'url' => ['blog/index']],
		['label' => Yii::t('blog', 'tab_popular'), 'url' => ['blog/popular']],
		['label' => Yii::t('blog', 'tab_discussed'), 'url' => ['blog/discussed']],
		['label' => Yii::t('blog', 'tab_subscribed'), 'url' => ['blog/subscribed']],
		['label' => Yii::t('blog', 'button_add'), 'icon' => 'glyphicon glyphicon-plus', 'url' =>  ['content/blog/create']],
	]
]) ?>

<div class="clearfix"></div>
