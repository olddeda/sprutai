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
		['label' => Yii::t('project', 'tab_newest'), 'url' => ['projects/default/index']],
		['label' => Yii::t('project', 'tab_popular'), 'url' => ['projects/default/popular']],
		['label' => Yii::t('project', 'tab_discussed'), 'url' => ['projects/default/discussed']],
		['label' => Yii::t('project', 'tab_subscribed'), 'url' => ['projects/default/subscribed']],
	]
]) ?>

<div class="clearfix"></div>
