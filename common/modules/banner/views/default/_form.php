<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\selectize\Selectize;
use common\modules\base\extensions\datetimepicker\DateTimePicker;
use common\modules\base\helpers\enum\Boolean;
use common\modules\base\helpers\enum\Status;

use common\modules\rbac\helpers\enum\Role;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\banner\models\Banner */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="banner-form">
	
	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<fieldset>
		<legend><?= Yii::t('banner', 'header_general') ?></legend>

		<div class="row margin-top-15">
			<div class="col-md-12">

				<div class="grid">
					<div class="col width-250">
						<?= $model->image->uploaderImage([
							'width' => 170,
							'height' => 170,
							'format' => Mode::CROP_CENTER,
						]); ?>
					</div>
					<div class="col width-auto">
						<?= $form->field($model, 'title') ?>
						
						<?= $form->field($model, 'url')->textInput(['maxlength' => 255]) ?>
						
						<?= $form->field($model, 'date_from')->widget(DateTimePicker::class, [
							'template' => '{input}{button}{reset}',
							'pickButtonIcon' => 'glyphicon glyphicon-calendar',
							'clientOptions' => [
								'autoclose' => true,
								'format' => 'dd-mm-yyyy',
								'todayBtn' => true,
								'minView' => 2,
							],
						]);?>
						
						<?= $form->field($model, 'date_to')->widget(DateTimePicker::class, [
							'template' => '{input}{button}{reset}',
							'pickButtonIcon' => 'glyphicon glyphicon-calendar',
							'clientOptions' => [
								'autoclose' => true,
								'format' => 'dd-mm-yyyy',
								'todayBtn' => true,
								'minView' => 2,
							],
						]);?>
					</div>
				</div>

			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?= Yii::t('banner', 'header_other') ?></legend>
		
		<?= $form->field($model, 'status')->widget(Select2::class, [
			'items' => Status::listData(),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
	</fieldset>

	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
		<?php if (Yii::$app->user->can('banner.default.index')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
				'class' => 'btn btn-default btn-lg'
			]) ?>
		<?php } ?>
	</div>
	
	<?php ActiveForm::end(); ?>
	
</div>

