<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

use common\modules\base\helpers\enum\ModuleType;

use artkost\qa\Module;

/** @var ActiveForm $form */
$form = ActiveForm::begin(['id' => 'answer-form', 'action' => $action]);
?>

<?= $form->errorSummary($model); ?>

<?= $form->field($model, 'content')->widget(common\modules\base\extensions\imperavi\Widget::className(), [
	'settings' => [
		'lang' => 'ru',
		'toolbar' => true,
		'buttons' => ['bold', 'italic', 'ul', 'ol', 'link', 'image'],
		'minHeight' => 200,
		'imageUpload' => Url::to([
			'/media/imperavi/upload',
			'module_type' => ModuleType::QA_ANSWER,
			'module_id' => $model->id,
		]),
		'imageManagerJson' => Url::to([
			'/media/imperavi/index',
			'module_type' => ModuleType::QA_ANSWER,
		]),
		'plugins' => [
			'imagemanager',
		],
	],
])->label(false) ?>

<?= Html::submitButton(($model->isNewRecord ? Yii::t('qa', 'button_answer_create') : Yii::t('qa', 'button_answer_update')), ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>