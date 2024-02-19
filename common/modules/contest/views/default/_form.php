<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\selectize\Selectize;
use common\modules\base\extensions\datetimepicker\DateTimePicker;

use common\modules\base\helpers\enum\Boolean;
use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\media\helpers\enum\Mode;

use common\modules\content\models\Content;
use common\modules\content\helpers\enum\Type as ContentType;

/* @var $this yii\web\View */
/* @var $model common\modules\contest\models\Contest */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contest-form">
	
	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<fieldset>
		<legend><?= Yii::t('contest', 'header_general') ?></legend>

		<div class="row margin-top-15">
			<div class="col-md-12">

				<div class="grid">
					<div class="col width-250">
						<?= $model->image->uploaderImageSlim([
							'settings' => [
								'size' => [
									'width' => 1000,
									'height' => 520,
								],
							],
							'format' => Mode::CROP_CENTER,
						]); ?>
					</div>
					<div class="col width-auto">
						<?= $form->field($model, 'title') ?>
						
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
		<legend><?= Yii::t('contest', 'header_module') ?></legend>
		
		<?= $form->field($model, 'module_type')->widget(Select2::class, [
			'items' => [ModuleType::CONTENT => ModuleType::getLabel(ModuleType::CONTENT)],
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
		
		<?= $form->field($model, 'module_id')->widget(Select2::class, [
			'items' => ArrayHelper::map(Content::find()->where([
				'and',
				['not in', 'status', [Status::TEMP, Status::DELETED]],
				['in', 'type', [ContentType::ARTICLE, ContentType::NEWS, ContentType::BLOG]],
			])->all(), 'id', 'titleType'),
			'clientOptions' => [
				'hideSearch' => false,
			]
		]) ?>
		
		<?= $form->field($model, 'is_paid')->widget(Select2::class, [
			'items' => Boolean::listData(),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
		
	</fieldset>

	<fieldset>
		<legend><?= Yii::t('contest', 'header_other') ?></legend>
		
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
		<?php if (Yii::$app->user->can('contest.default.index')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
				'class' => 'btn btn-default btn-lg'
			]) ?>
		<?php } ?>
	</div>
	
	<?php ActiveForm::end(); ?>
	
</div>

