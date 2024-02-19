<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

use common\modules\base\extensions\selectize\Selectize;

use common\modules\rbac\models\forms\AssignForm;

// Create assign form
$assignModel = new AssignForm;
$assignModel->parent = $model->name;

?>

<div class="rbac-assign">
	<fieldset>
		<legend><?= Yii::t('rbac', 'header_assign') ?></legend>
		
		<?php $form = ActiveForm::begin([
			'enableClientValidation' => true,
			'enableAjaxValidation' => false,
			'action' => [
				'/rbac/'.$model->typeName.'/assign',
				'name' => $model->name,
			],
		]) ?>
		
		<?= $form->field($model, 'type')->hiddenInput() ?>

		<div class="row form-group">
			<div class="col-md-10">
				<?= $form->field($assignModel, 'child', ['options' => ['class' => 'required']])->widget(Selectize::className(), [
					'items' => $model->getTree(),
					'pluginOptions' => [
						'persist' => false,
						'createOnBlur' => false,
						'create' => false,
					]
				])->label(false); ?>
			</div>
			<div class="col-md-2">
				<?= Html::submitButton(Yii::t('rbac', 'button_assign'), [
					'class' => 'btn btn-block btn-primary'
				]) ?>
			</div>
		</div>
	</fieldset>

	<?php ActiveForm::end() ?>
</div>