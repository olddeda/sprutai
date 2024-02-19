<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

use common\modules\base\helpers\enum\Status;


/* @var $this yii\web\View */
/* @var $model common\modules\paste\models\Paste */
/* @var $form yii\widgets\ActiveForm */
?>

<?php

$js = <<<JS
var selectObj = $('#paste-mode');
selectObj.on('change', function() {
	aceeditor_w1.session.setMode({
		path: 'ace/mode/' + $(this).val(),
		inline: true
	});
});

var modelist = ace.require('ace/ext/modelist');
$.each(modelist.modesByName, function(key, obj) {
	var selected = (key == '{$model->mode}');
    var option = $('<option>').val(key).text(obj.caption);
    if (selected)
    	option.attr('selected', 'selected');
    option.appendTo(selectObj);
    
})
JS;
$this->registerJs($js);

?>
<div class="paste-form">
	
	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<fieldset>
		<legend><?= Yii::t('paste', 'header_general') ?></legend>
		
		<?= $form->field($model, 'descr')->textInput([
			'placeholder' => Yii::t('paste', 'placeholder_descr'),
		]) ?>
		
		<?= $form->field($model, 'is_private')->checkbox() ?>
		
		<?= $form->field($model, 'mode')->widget(\common\modules\base\extensions\select2\Select2::class, [
			'items' => [],
			'clientOptions' => [
				'hideSearch' => false,
			]
		]) ?>
		
	</fieldset>

	<fieldset>
		<legend><?= Yii::t('paste', 'header_code') ?></legend>
		
		<?= $form->field($model, 'code')->widget(\common\modules\base\extensions\aceeditor\AceEditor::class, [
			'mode' => $model->mode,
			'value' => Html::decode($model->code),
			'pluginOptions' => [
				'minLines' => 20,
				'maxLines' => 100,
                'autoScrollEditorIntoView' => true,
			],
		])->label(false) ?>
		
	</fieldset>

	<fieldset>
		<legend><?= Yii::t('paste', 'header_other') ?></legend>
		
		<?= $form->field($model, 'status')->widget(\common\modules\base\extensions\select2\Select2::class, [
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
		<?php if (Yii::$app->user->can('paste.default.index')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
				'class' => 'btn btn-default btn-lg'
			]) ?>
		<?php } ?>
	</div>
	
	<?php ActiveForm::end(); ?>
	
</div>

