<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\Boolean;
use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\media\models\MediaFormat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="media-format-form">

	<?php $form = ActiveForm::begin(); ?>

	<fieldset>
		<legend><?= Yii::t('media-format', 'header_general') ?></legend>

		<div class="row">
			<div class="col-md-3">
				<?= $form->field($model, 'width')->textInput() ?>
			</div>
			<div class="col-md-3">
				<?= $form->field($model, 'height')->textInput() ?>
			</div>
			<div class="col-md-3">
				<?= $form->field($model, 'mode')->dropDownList(Mode::listData()) ?>
			</div>
			<div class="col-md-3">
				<?= $form->field($model, 'watermark')->dropDownList(Boolean::listData()) ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?= Yii::t('media-format', 'header_other') ?></legend>
	
		<?= $form->field($model, 'status')->dropDownList(Status::listData()) ?>
	</fieldset>

	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => $model->isNewRecord ? 'btn btn-primary btn-lg' : 'btn btn-primary btn-lg'
		]) ?>
		<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
			'class' => 'btn btn-default btn-lg'
		]) ?>
	</div>

    <?php ActiveForm::end(); ?>

</div>
