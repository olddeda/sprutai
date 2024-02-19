<?php

/**
 * @var $this yii\web\View
 * @var $model common\modules\rbac\models\Role
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>

<div class="role-form">

	<?php $form = ActiveForm::begin([
		'enableClientValidation' => false,
		'enableAjaxValidation' => true,
	]) ?>

	<fieldset>
		<legend><?= Yii::t('rbac-permission', 'header_general') ?></legend>
	</fieldset>

	<?= $form->field($model, 'name') ?>
	<?= $form->field($model, 'description') ?>
	<?= $form->field($model, 'rule') ?>

	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.(!$model->name ? Yii::t('base', 'button_create') : Yii::t('base', 'button_update')), [
			'class' => !$model->name ? 'btn btn-success btn-lg' : 'btn btn-primary btn-lg'
		]) ?>
		<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
			'class' => 'btn btn-default btn-lg'
		]) ?>
	</div>

	<?php ActiveForm::end() ?>
</div>