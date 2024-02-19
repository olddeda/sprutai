<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\modules\base\helpers\enum\Status;
use common\modules\settings\helpers\enum\Type;

/* @var $this yii\web\View */
/* @var $model common\modules\settings\models\Settings */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="setting-form">

    <?php $form = ActiveForm::begin(); ?>

	<fieldset>
		<legend><?= Yii::t('settings', 'header_general') ?></legend>

   		 	<?php echo $form->field($model, 'section')->textInput(['maxlength' => 255]) ?>
    		<?php echo $form->field($model, 'key')->textInput(['maxlength' => 255]) ?>
    		<?php echo $form->field($model, 'value')->textInput(['maxlength' => 255]) ?>
			<?php echo $form->field($model, 'descr')->textInput(['maxlength' => 255]) ?>

	</fieldset>

	<fieldset class="margin-top-10">
		<legend><?= Yii::t('settings', 'header_other') ?></legend>
		
    	<?php echo $form->field($model, 'status')->dropDownList(Status::listData()); ?>
		<?php echo $form->field($model, 'type')->dropDownList(Type::listData()); ?>

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
