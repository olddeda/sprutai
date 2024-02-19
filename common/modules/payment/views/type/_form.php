<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

use common\modules\base\extensions\select2\Select2;

use common\modules\content\helpers\enum\Status;

use common\modules\payment\models\PaymentType;
use common\modules\payment\helpers\enum\Kind;

/* @var $this yii\web\View */
/* @var $model common\modules\payment\models\PaymentType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-type-form">
	
	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<fieldset>
		<legend><?= Yii::t('payment-type', 'header_general') ?></legend>

		<div class="row margin-top-15">
			<div class="col-md-12">
				
				<?= $form->field($model, 'kind')->widget(Select2::class, [
					'items' => Kind::listData(),
					'clientOptions' => [
						'hideSearch' => true,
					]
				]) ?>
				
				<?= $form->field($model, 'title') ?>
				
				<?= $form->field($model, 'descr')->widget(common\modules\base\extensions\imperavi\Widget::class, [
					'settings' => [],
				]) ?>
				
				<?= $form->field($model, 'price')->widget(\kartik\number\NumberControl::class, [
					'maskedInputOptions' => [
						'suffix' => ' ₽',
						'digits' => 0,
						'groupSeparator' => ' ',
						'rightAlign' => false,
					],
					'displayOptions' => [
						'placeholder' => Yii::t('payment-type', 'placeholder_price'),
					],
				]) ?>
				
				<?= $form->field($model, 'price_tax')->widget(\kartik\number\NumberControl::class, [
					'maskedInputOptions' => [
						'suffix' => ' %',
						'digits' => 0,
						'groupSeparator' => ' ',
						'rightAlign' => false,
					],
					'displayOptions' => [
						'placeholder' => Yii::t('payment-type', 'placeholder_price_tax'),
					],
				]) ?>
				
				<?= $form->field($model, 'price_fixed')->checkbox() ?>
				
				<?= $form->field($model, 'physical')->checkbox() ?>
			</div>
		</div>
	</fieldset>
	
	<fieldset>
		<legend><?= Yii::t('payment-type', 'header_other') ?></legend>
		
		<?= $form->field($model, 'identifier') ?>
		
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
		<?php if (Yii::$app->user->can('payment.type.index')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
				'class' => 'btn btn-default btn-lg'
			]) ?>
		<?php } ?>
	</div>
	
	<?php ActiveForm::end(); ?>

</div>