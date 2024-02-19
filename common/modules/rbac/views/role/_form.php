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
		<legend><?= Yii::t('rbac-role', 'header_general') ?></legend>
		
		<?= $form->field($model, 'name') ?>
		<?= $form->field($model, 'description') ?>
		<?= $form->field($model, 'rule') ?>
	</fieldset>

	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.(!$model->name ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => !$model->name ? 'btn btn-primary btn-lg' : 'btn btn-primary btn-lg'
		]) ?>
		<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
			'class' => 'btn btn-default btn-lg'
		]) ?>
	</div>

	<?php ActiveForm::end() ?>
</div>