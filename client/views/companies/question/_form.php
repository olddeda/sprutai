<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use common\modules\base\helpers\enum\ModuleType;

/* @var $this yii\web\View */
/* @var $company \common\modules\company\models\Company */
/* @var $model \common\modules\content\models\Question */
/* @var $isCreate bool */

?>

<div class="companies-question-form">
	<div class="panel panel-default">
		<div class="panel-body">
			
			<?php $form = ActiveForm::begin([
				'id' => 'form-content',
				'options' => ['enctype' => 'multipart/form-data'],
				'enableClientValidation' => true,
				//'enableAjaxValidation' => true,
			]); ?>
			
			<fieldset>
				<?= $form->field($model,'title') ?>
				
				<?php
				$buttons = [];
				if (Yii::$app->user->getIsAdmin())
					$buttons[] = 'html';
				$buttons = ArrayHelper::merge($buttons, ['bold', 'italic', 'ul', 'ol', 'link', 'image']);
				?>
				
				<?= $form->field($model, 'text')->widget(\common\modules\base\extensions\imperavi\Widget::class, [
					'value' => $model->text,
					'settings' => [
						'lang' => 'ru',
						'toolbar' => true,
						'focus' => true,
						'buttons' => $buttons,
						'minHeight' => '200px',
						'imageUpload' => Url::to(['/media/imperavi/upload', 'module_type' => ModuleType::CONTENT_QUESTION, 'module_id' => $model->id]),
						'imageManagerJson' => Url::to(['/media/imperavi/index', 'module_type' => ModuleType::CONTENT_QUESTION]),
						'plugins' => [
							'imagemanager',
						],
					],
				]) ?>
			</fieldset>
			
			<div class="form-group margin-top-40 margin-bottom-0 align-center">
				<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($isCreate ? Yii::t('company-question', 'button_create') : Yii::t('company-question', 'button_save')), [
					'id' => 'button-content-submit',
					'class' => 'btn btn-primary btn-lg',
				]) ?>
				<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('company-question', 'button_back'), ['index', 'company_id' => $company->id], [
					'class' => 'btn btn-default btn-lg'
				]) ?>
			</div>
			
			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
