<?php

use common\modules\base\extensions\datetimepicker\DateTimePicker;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\helpers\enum\Status;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\shortener\models\Shortener */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shortener-form">
	
	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<fieldset>
		<legend><?= Yii::t('shortener', 'header_general') ?></legend>
        
        <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

		<div class="row">
			<div class="col-md-8">
                <?= $form->field($model, 'url')->textInput(['maxlength' => 1000]) ?>
			</div>
			<div class="col-md-4">
                <?= $form->field($model, 'expiration_date')->widget(DateTimePicker::class, [
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
        
        <?= $form->field($model, 'description')->textarea(['rows' => 7, 'value' => html_entity_decode($model->description)]) ?>
			
	</fieldset>

	<fieldset>
		<legend><?= Yii::t('shortener', 'header_other') ?></legend>
		
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
		<?php if (Yii::$app->user->can('shortener.default.index')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
				'class' => 'btn btn-default btn-lg'
			]) ?>
		<?php } ?>
	</div>
	
	<?php ActiveForm::end(); ?>
	
</div>

