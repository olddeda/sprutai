<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use common\modules\base\extensions\select2\Select2;

use common\modules\base\helpers\enum\ModuleType;

/* @var $this yii\web\View */
/* @var $model common\modules\seo\models\SeoModule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="page-form">

    <?php $form = ActiveForm::begin([
		'id' => 'page-form',
		'enableAjaxValidation' => false,
	]); ?>

	<fieldset>
		<legend><?= Yii::t('content-page', 'header_general') ?></legend>

		<div class="row margin-top-15">
			<div class="col-md-12">
				
				<?= $form->field($model, 'module_type')->widget(Select2::className(), [
					'items' => ModuleType::listData(),
					'clientOptions' => [
						'hideSearch' => true,
					]
				]) ?>
				
				<?= $form->field($model, 'module_class')->textInput(['maxlength' => true]) ?>
				<?= $form->field($model, 'slugify')->textInput(['maxlength' => true]) ?>
				
			</div>
		</div>
	</fieldset>
	

	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
		<?php if (Yii::$app->user->can('seo.module.index')) { ?>
		<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
			'class' => 'btn btn-default btn-lg'
		]) ?>
		<?php } ?>
	</div>

    <?php ActiveForm::end(); ?>

</div>
