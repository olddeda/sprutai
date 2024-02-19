<?php

use yii\widgets\ActiveForm;
use yii\helpers\Url;

use artkost\qa\components\ActiveField;
use artkost\qa\Module;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\tag\models\Qa;

?>

<div class="ask-form">
	<?php $form = ActiveForm::begin([
		'id' => 'question-form',
		'fieldConfig' => ['class' => ActiveField::className()]
	]); ?>
	
	<?= $form->errorSummary($model); ?>

	<fieldset>
		<legend><?= Yii::t('qa', 'header_general') ?></legend>
		
		<?= $form->field($model, 'title')->textInput(); ?>
		
		<?= $form->field($model, 'content')->widget(common\modules\base\extensions\imperavi\Widget::className(), [
			'settings' => [
				'lang' => 'ru',
				'toolbar' => true,
				'buttons' => ['bold', 'italic', 'ul', 'ol', 'link', 'image'],
				'minHeight' => 500,
				'imageUpload' => Url::to([
					'/media/imperavi/upload',
					'module_type' => ModuleType::QA_QUESTION,
					'module_id' => $model->id,
				]),
				'imageManagerJson' => Url::to([
					'/media/imperavi/index',
					'module_type' => ModuleType::QA_QUESTION,
				]),
				'plugins' => [
					'imagemanager',
				],
			],
		]) ?>
		
	</fieldset>

	<fieldset>
		<legend><?= Yii::t('qa', 'header_other') ?></legend>
		
		<?= $form->field($model, 'tags')->widget(Select2::className(), [
			'items' => Qa::listData('title', 'title'),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>

	</fieldset>

	<div class="form-group">
		<div class="btn-group btn-group-lg">
			<button type="submit" name="draft" class="btn"><?= Module::t('main', 'Draft') ?></button>
			<?php if ($model->isNewRecord): ?>
				<button type="submit" name="submit" class="btn btn-primary"><?= Module::t('main', 'Publish') ?></button>
			<?php else: ?>
				<button type="submit" name="update" class="btn btn-success"><?= Module::t('main', 'Update') ?></button>
			<?php endif; ?>
		</div>
	</div>
	
	<?php ActiveForm::end(); ?>
	
</div>