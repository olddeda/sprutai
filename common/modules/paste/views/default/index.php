<?php

use yii\helpers\Html;
use yii\widgets\ListView;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('paste', 'title');

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="paste-index">
	
	<?php if ($dataProvider->totalCount) { ?>
	<?= ListView::widget([
		'dataProvider' => $dataProvider,
		'itemView' => '_view',
		'layout' => "{items}\n{pager}",
	]); ?>
	<?php } ?>

	<?php if (Yii::$app->user->can('paste.default.create')) { ?>
	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>
	<?php } ?>

</div>
