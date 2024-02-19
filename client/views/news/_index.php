<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $type string */

use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\Url;

$emptyText = Yii::t('news', 'error_not_found_'.$type, [
	'url_authors' => Url::to(['/author/index']),
	'url_tags' => Url::to(['/tags/index']),
]);
$emptyText = Html::tag('div', Html::tag('div', $emptyText, ['class' => 'panel-body']), ['class' => 'panel panel-default']);
?>

<div class="content-index">
	<?= ListView::widget([
		'dataProvider' => $dataProvider,
		'itemView' => '_view',
		'layout' => "{items}\n{pager}",
		'emptyText' => $emptyText,
	]); ?>
</div>

<div class="form-group margin-top-20">
	<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('news', 'button_add'), ['/content/news/create'], [
		'class' => 'btn btn-primary btn-lg'
	]) ?>
</div>

