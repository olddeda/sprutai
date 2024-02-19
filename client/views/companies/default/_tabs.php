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
		['label' => Yii::t('company', 'tab_all'), 'url' => ['companies/default/index']],
		['label' => Yii::t('company', 'tab_vendors'), 'url' => ['companies/default/vendors']],
		['label' => Yii::t('company', 'tab_integrators'), 'url' => ['companies/default/integrators']],
		['label' => Yii::t('company', 'tab_shops'), 'url' => ['companies/default/shops']],
	]
]) ?>

<div class="clearfix"></div>
