<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use common\modules\base\helpers\enum\ModuleType;

/* @var $this yii\web\View */
/* @var $project \common\modules\project\models\Project */
/* @var $model \common\modules\content\models\Question */
/* @var $isCreate bool */

?>

<div class="projects-question-form">
	<div class="panel panel-default">
		<div class="panel-body">
			
			<?php $form = ActiveForm::begin([
				'id' => 'form-content',
				'options' => ['enctype' => 'multipart/form-data'],
				'enableClientValidation' => true,
				//'enableAjaxValidation' => true,
			]); ?>
			
			<fieldset>
				<legend><?= Yii::t('project-question', 'header_text') ?></legend>
				
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
				])->label(false) ?>
			</fieldset>
			
			<div class="form-group margin-top-40 margin-bottom-0 align-center">
				<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($isCreate ? Yii::t('project-question', 'button_create') : Yii::t('project-question', 'button_save')), [
					'id' => 'button-content-submit',
					'class' => 'btn btn-primary btn-lg',
				]) ?>
				<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('project-question', 'button_back'), ['index', 'project_id' => $project->id], [
					'class' => 'btn btn-default btn-lg'
				]) ?>
			</div>
			
			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
