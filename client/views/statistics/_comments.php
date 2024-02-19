<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\modules\base\components\ActiveRecord */

$comments = ($model->stat) ? $model->stat->comments : 0;
?>

<div class="statistics-views inline" data-toggle="tooltip" data-original-title="<?= Yii::t('statistics', 'tip_comments', ['n' => $comments]) ?>">
	<?= Html::tag('span', '', [
		'class' => 'fa fa-comments',
	]) ?>
	<?= Html::tag('b', $comments) ?>
</div>