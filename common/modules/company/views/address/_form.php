<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\helpers\enum\Boolean;

use common\modules\base\extensions\dadataru\DaDataRu;

/* @var $this yii\web\View */
/* @var $model common\modules\user\models\UserAddress */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$js = <<<JS
function addressSelect(q) {
    var regionTmp = [];
    if (q.data.region) {
        if (q.data.region_type == 'г')
            regionTmp.push(q.data.region_type + '. ' + q.data.region);
        else
            regionTmp.push(q.data.region + ' ' + q.data.region_type + '.');
    }
    if (q.data.area)
        regionTmp.push(q.data.area_type + '. ' + q.data.area);
    var region = regionTmp.join(', ');
    
    var cityTmp = [];
    if (q.data.settlement)
        cityTmp.push(q.data.settlement_type + '. ' + q.data.settlement);
    if (q.data.city)
        cityTmp.push(q.data.city_type + '. ' + q.data.city);
    var city = cityTmp.join(', ');
    
    var streetTmp = [];
    if (q.data.street)
        streetTmp.push(q.data.street_type + '. ' + q.data.street);
    var street = streetTmp.join(', ');
    
    var houseTmp = [];
    if (q.data.house)
        houseTmp.push(q.data.house_type + '. ' + q.data.house);
    var house = houseTmp.join(', ');
    
    var blockTmp = [];
    if (q.data.block)
        blockTmp.push(q.data.block_type + '. ' + q.data.block);
    var block = blockTmp.join(', ');
    
    var flatTmp = [];
    if (q.data.flat)
       flatTmp.push(q.data.flat_type + '. ' + q.data.flat);
    var flat = flatTmp.join(', ');
    
    $('#companyaddress-postal_code').val(q.data.postal_code);
    $('#companyaddress-country').val(q.data.country);
    $('#companyaddress-region').val(region);
    $('#companyaddress-city').val(city);
    $('#companyaddress-street').val(street);
    $('#companyaddress-house').val(house);
    $('#companyaddress-block').val(block);
    $('#companyaddress-flat').val(flat);
    
    $('#address-fields').show();
    
    $('#btn-submit').removeAttr('disabled');
}
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>

<div class="company-user-form">

	<?php $form = ActiveForm::begin([
		'id' => 'company-address-form',
		'enableAjaxValidation' => false,
		'enableClientValidation' => true,
	]); ?>
	
	<div class="margin-top-20">
		<?= $form->field($model, 'address')->widget(DaDataRu::class, [
			'token' => Yii::$app->params['datadataru.token'],
			'type' => DaDataRu::TYPE_ADDRESS,
			'options' => [
				'class' => 'form-control'
			],
			'pluginOptions' => [
				'onSelect' => new JsExpression('addressSelect'),
			],
		]); ?>
	</div>
	
	<div id="address-fields" style="display: <?= $model->isNewRecord ? 'none' : 'block'; ?>">
		<hr />
		
		<?= $form->field($model, 'postal_code'); ?>
		<?= $form->field($model, 'country'); ?>
		<?= $form->field($model, 'region'); ?>
		<?= $form->field($model, 'city'); ?>
		<?= $form->field($model, 'street'); ?>
		<?= $form->field($model, 'house'); ?>
		<?= $form->field($model, 'block'); ?>
		<?= $form->field($model, 'flat'); ?>
	
	</div>
	
	<hr />

	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
		<?php if (Yii::$app->user->can('company.user.index')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index', 'company_id' => $company->id], [
				'class' => 'btn btn-default btn-lg'
			]) ?>
		<?php } ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
