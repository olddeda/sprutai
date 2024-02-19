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
    
    $('#useraddress-postal_code').val(q.data.postal_code);
    $('#useraddress-country').val(q.data.country);
    $('#useraddress-region').val(region);
    $('#useraddress-city').val(city);
    $('#useraddress-street').val(street);
    $('#useraddress-house').val(house);
    $('#useraddress-block').val(block);
    $('#useraddress-flat').val(flat);
    
    $('#address-fields').show();
    
    $('#btn-submit').removeAttr('disabled');
}
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>

<?php $form = ActiveForm::begin([
	'id' => 'account-form',
	'options' => ['class' => 'form-horizontal'],
	'fieldConfig' => [
		'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
		'labelOptions' => ['class' => 'col-lg-3 control-label'],
	],
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
	
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('user-address', 'button_create') : Yii::t('user-address', 'button_save')), [
				'id' => 'btn-submit',
				'class' => 'btn btn-primary btn-lg',
				'disabled' => $model->isNewRecord,
			]) ?>
			<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
				'class' => 'btn btn-default btn-lg'
			]) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>