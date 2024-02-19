<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\paste\models\Paste */

?>

<div class="panel panel-default">
	<div class="panel-heading">
		<div>
			<?= Html::a($model->getLink(), $model->getLink(), ['class' => 'inline', 'target' => '_blank']) ?>
			<?php if ($model->is_private) { ?>
			<?= Html::tag('span', Yii::t('paste', 'private'), ['class' => 'badge badge-danger']) ?>
			<?php } ?>
		</div>
		<?php if ($model->descr) { ?>
		<div class="margin-top-5">
			<i><?= Html::decode($model->descr) ?></i>
		</div>
		<?php } ?>
	</div>
	
	<div class="panel-body padding-top-0">
		<?= \common\modules\base\extensions\aceeditor\AceEditor::widget([
			'name' => 'paste',
			'value' => Html::decode($model->code),
			'mode' => $model->mode,
			'readOnly' => true,
			'pluginOptions' => [
				'maxLines' => 5,
			],
			'options' => [
				'id' => 'paste'.$model->id,
			],
		]) ?>
	</div>
	
	<div class="panel-footer">
		<div class="form-group margin-0">
			<?php if (Yii::$app->user->can('paste.default.update')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('base', 'button_update'), ['update', 'id' => $model->id], [
				'class' => 'btn btn-primary btn-sm'
			]) ?>
			<?php } ?>
			<?php if (Yii::$app->user->can('paste.default.delete')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('base', 'button_delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger btn-sm',
				'data' => [
					'confirm' => Yii::t('paste', 'confirm_delete'),
					'method' => 'post',
				],
			]) ?>
			<?php } ?>
		</div>
	</div>
</div>
