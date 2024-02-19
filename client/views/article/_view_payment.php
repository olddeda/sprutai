<?php

use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Json;

use kartik\number\NumberControl;

use common\modules\base\extensions\select2\Select2;

use common\modules\payment\models\Payment;
use common\modules\payment\models\PaymentType;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Article */
/* @var $paymentModel common\modules\payment\models\Payment */

?>

<?php

$headerTitle = (isset($headerTitle)) ? $headerTitle : Yii::t('article', 'popup_payment_title');
$buttonText = (isset($buttonText)) ? $buttonText : Yii::t('article', 'popup_payment_button_open');

/** @var \common\modules\payment\models\PaymentType $typeModel */
$typeModel = PaymentType::findByIdentifier('author', true);
	
// Create model
$paymentModel = new Payment();
$paymentModel->module_type = $model->getModuleType();
$paymentModel->module_id = $model->id;
$paymentModel->payment_type_id = $typeModel->id;
$paymentModel->price = (int)$typeModel->price;

$paymentTypes = [
	$typeModel->id => [
		'price' => $typeModel->price,
		'fixed' => $typeModel->price_fixed,
		
	]
];
$paymentTypesJson = Json::encode($paymentTypes);

$js =<<<JS

	var types = $paymentTypesJson;

	$('#payment-payment_type_id').change(function() {
		var typeId = $(this).val();
		var type = types[typeId];
		
		$('#payment-price').val(type.price);
		if (type.fixed)
			$('#payment-price').attr('readonly', true);
		else
			$('#payment-price').removeAttr('readonly');
		
	});
JS;
$this->registerJs($js);

?>

<?php if (Yii::$app->user->isGuest) { ?>
<?= Html::a(Yii::t('article', 'popup_payment_button_open'), ['user/signin'], [
	'class' => 'btn btn-primary btn-lg'
]) ?>
<?php } else { ?>

<?php Modal::begin([
	'header' => Html::tag('h4', $headerTitle, ['class' => 'margin-0']),
	'toggleButton' => [
		'label' => $buttonText,
		'tag' => 'button',
		'class' => 'btn btn-primary btn-lg',
	],
]); ?>

<?php $form = ActiveForm::begin([
	'id' => 'payment-form',
	'enableAjaxValidation' => false,
	'action' => ['/payment/gateway/create'],
]); ?>

<?= Html::activeHiddenInput($paymentModel, 'module_type') ?>
<?= Html::activeHiddenInput($paymentModel, 'module_id') ?>

<div class="row">
	<div class="col-md-12">
		<?php if (count($model->getPayment_types_ids()) > 1) { ?>
		<?= $form->field($paymentModel, 'payment_type_id')->widget(Select2::class, [
			'items' => ArrayHelper::map($model->paymentTypes, 'id', 'title'),
			'options' => [
				'prompt' => Yii::t('payment-type-module', 'placeholder_payment_type_id'),
			],
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
		<?php } else { ?>
		<?= Html::activeHiddenInput($paymentModel, 'payment_type_id') ?>
		<?php } ?>
		
		<?= $form->field($paymentModel, 'price', [
			'inputTemplate' => '<div class="input-group">{input}<span class="input-group-addon"><span class="glyphicon glyphicon-rub" aria-hidden="true"></span></div>',
		])->input('text', ['placeholder' => Yii::t('payment-type-module', 'placeholder_price')])?>
	</div>
</div>

<?= Html::submitButton(Yii::t('payment-type-module', 'button_payment'), [
	'class' => 'btn btn-primary btn-lg'
]) ?>

<?php /*
<div class="align-center margin-top-20">
	<?= Yii::t('payment', 'tip_confirmation', ['link' => Html::a(Yii::t('payment', 'tip_confirmation_link'), ['/client/terms-of-payment'])]) ?>
</div>
 */ ?>

<?php ActiveForm::end(); ?>

<?php Modal::end(); ?>

<?php } ?>
