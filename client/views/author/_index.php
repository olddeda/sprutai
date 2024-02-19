<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\widgets\ListView;
use yii\helpers\Html;

?>

<div class="form-group margin-bottom-20">
	<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('author', 'button_join'), ['/content/article/create'], [
		'class' => 'btn btn-primary btn-lg'
	]) ?>
</div>

<div class="content-index">
	<?= ListView::widget([
		'dataProvider' => $dataProvider,
		'itemView' => '_view',
		'layout' => "{items}\n{pager}"
	]); ?>
</div>

<div class="form-group margin-top-20">
	<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('author', 'button_join'), ['/content/article/create'], [
		'class' => 'btn btn-primary btn-lg'
	]) ?>
</div>

