<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\modules\base\helpers\enum\Status;

use common\modules\lookup\models\Lookup;

/* @var $this yii\web\View */
/* @var $model common\modules\lookup\models\Lookup */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$lastSequences = json_encode(Lookup::lastSequences());
$typesConfig = json_encode(Lookup::typesConfig());
	
$js = <<< JS
	var lastSequences = $lastSequences;
	var typesConfig = $typesConfig;
	
	function isUse(type, field) {
		if (typesConfig[type] && typesConfig[type]['use'] && typesConfig[type]['use'][field])
			return typesConfig[type]['use'][field];
		return false;
	}
	
	function showOrHide(field, yesOrNo) {
		if (yesOrNo)
			field.removeClass('hide').addClass('show');
		else 
			field.removeClass('show').addClass('hide');
	}
	
	$('#lookup-type').change(function() {
		var type = $(this).val();
		
		// Set visible parent_id
		showOrHide($('#lookup_parent_id'), isUse(type, 'parent_id'));
		
		// Set visible code
		showOrHide($('#lookup_code'), isUse(type, 'code'));
		
		// Set visible text
		showOrHide($('#lookup_text'), isUse(type, 'text'));
		
		// Set last sequence
		var sequence = (lastSequences[type]) ? lastSequences[type] : 1;
		$('#lookup-sequence').val(sequence);
	})
JS;

$this->registerJs($js);
?>

<div class="lookup-form">

    <?php $form = ActiveForm::begin([
    	'enableClientValidation' => true,
		'enableAjaxValidation' => false,
	]); ?>
	
	<fieldset>
		<legend><?= Yii::t('lookup', 'header_general') ?></legend>

		<div class="row">
			<div class="col-md-12">
				
				<?= $form->field($model, 'type')->dropDownList(Lookup::types(), [
					'prompt' => Yii::t('lookup', 'prompt_type'),
				]) ?>

				<div id="lookup_parent_id" class="<?= (!Lookup::isUseField($model->type, 'parent_id')) ? 'hide' : 'show' ?>">
					<?= $form->field($model, 'parent_id')->textInput() ?>
				</div>
				<?= $form->field($model, 'title')->textInput() ?>

				<div id="lookup_code" class="<?= (!Lookup::isUseField($model->type, 'code')) ? 'hide' : 'show' ?>">
					<?= $form->field($model, 'code')->textInput() ?>
				</div>
				
				<?= $form->field($model, 'sequence')->textInput() ?>
			</div>
		</div>
	</fieldset>
	
	<div id="lookup_text" class="<?= (!Lookup::isUseField($model->type, 'text')) ? 'hide' : 'show' ?>">
		<fieldset class="margin-top-20">
			<legend><?= Yii::t('lookup', 'header_text') ?></legend>
			
			<?= $form->field($model, 'text')->textarea(['rows' => 5]) ?>
		</fieldset>
	</div>
	
	<fieldset class="margin-top-20">
		<legend><?= Yii::t('lookup', 'header_other') ?></legend>
		
		<?= $form->field($model, 'status')->dropDownList(Status::listData()) ?>
	</fieldset>

	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => $model->isNewRecord ? 'btn btn-success btn-lg' : 'btn btn-primary btn-lg'
		]) ?>
		<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
			'class' => 'btn btn-default btn-lg'
		]) ?>
	</div>

    <?php ActiveForm::end(); ?>

</div>
