<?php
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

?>

<div>
	<?php $form = ActiveForm::begin([
		'action' => ['/media/default/modal'],
		'options' => ['id' => 'fileinput-widget-form'],
		'enableAjaxValidation' => false,
	]); ?>
	
	<?= Html::hiddenInput('media_hash', $model->hash) ?>
	
	<?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
	<?= $form->field($model, 'alt')->textInput(['maxlength' => 255]) ?>
	<?= $form->field($model, 'descr')->textarea(['rows' => 5]) ?>
	<?= $form->field($model, 'sequence')->textInput() ?>
	<?= $form->field($model, 'is_main', [
		'template' => '<div class="checkbox-default checkbox-primary">{input}{label}</div>',
	])->checkbox([], false) ?>
	
	<?php ActiveForm::end(); ?>
</div>
