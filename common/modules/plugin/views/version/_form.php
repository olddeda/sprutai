<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\datetimepicker\DateTimePicker;
use common\modules\base\helpers\enum\Status;

/* @var $this yii\web\View */
/* @var $model common\modules\plugin\models\Version */
/* @var $plugin common\modules\plugin\models\Plugin */
/* @var $form yii\widgets\ActiveForm */
/* @var $isCreate boolean */
?>

<div class="plugin-version-form">

    <?php $form = ActiveForm::begin([
		'id' => 'version-form',
		'enableAjaxValidation' => false,
	]); ?>

	<fieldset>
		<legend><?= Yii::t('plugin-version', 'header_general') ?></legend>

		<div class="row">
			<div class="col-md-12">
				
				<?= $form->field($model, 'datetime')->widget(DateTimePicker::class, [
					'template' => '{input}',
					'pickButtonIcon' => 'glyphicon glyphicon-calendar',
					'clientOptions' => [
						'autoclose' => true,
						'format' => 'dd-mm-yyyy hh:ii',
						'todayBtn' => true,
					],
				]);?>
				
				<?php if ($model->repository->provider == \common\modules\plugin\helpers\enum\RepositoryProvider::MANUAL) { ?>
				<?= $form->field($model, 'file')->fileInput()->hint(Yii::t('plugin-version', 'field_hint_file')) ?>
				<?php } ?>
				
				<?php if ($model->repository->provider == \common\modules\plugin\helpers\enum\RepositoryProvider::URL) { ?>
					<?= $form->field($model, 'url', ['options' => ['class' => 'required']])->textInput()->hint(Yii::t('plugin-version', 'field_hint_url_file')) ?>
				<?php } ?>

				<?= $form->field($model, 'version')->textInput(['readonly' => $model->repository->provider == \common\modules\plugin\helpers\enum\RepositoryProvider::GITHUB]) ?>
    
				<?= $form->field($model, 'text')->widget(common\modules\base\extensions\imperavi\Widget::class, [
					'settings' => [
						'lang' => 'ru',
						'toolbar' => true,
						'focus' => true,
						'buttons' => ['bold', 'italic', 'ul', 'ol', 'link'],
						'minHeight' => 300,
						
						'imageUpload' => Url::to([
							'/media/imperavi/upload',
							'module_type' => \common\modules\base\helpers\enum\ModuleType::PLUGIN_VERSION,
							'module_id' => $model->id,
						]),
						'imageManagerJson' => Url::to([
							'/media/imperavi/index',
							'module_type' => \common\modules\base\helpers\enum\ModuleType::PLUGIN_VERSION,
						]),
						'plugins' => [
							//'imagemanager',
						],
					],
				])->label()->hint(Yii::t('plugin-version', 'field_hint_text')) ?>
				
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?= Yii::t('plugin-version', 'header_other') ?></legend>
		
		<?= $form->field($model, 'status')->widget(Select2::class, [
			'items' => Status::listData(),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
	</fieldset>

	<div class="form-group margin-top-30">
		<div class="pull-left">
			<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
				'class' => 'btn btn-primary btn-lg'
			]) ?>
			<?php if (!$isCreate && Yii::$app->user->can('plugin.version.index')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index', 'plugin_id' => $plugin->id], [
				'class' => 'btn btn-default btn-lg'
			]) ?>
			<?php } ?>
		</div>
		<div class="pull-right">
			<?php if ($model->repository->provider == \common\modules\plugin\helpers\enum\RepositoryProvider::GITHUB) { ?>
			<?php if (Yii::$app->user->can('plugin.version.select-release')) { ?>
				<?= Html::a('<span class="glyphicon glyphicon-refresh"></span> '.Yii::t('plugin-version', 'button_release_change'), ArrayHelper::merge(['select-release'], $this->context->urlParams), [
					'class' => 'btn btn-default btn-lg'
				]) ?>
			<?php } ?>
			<?php } else { ?>
				<?= Html::a('<span class="glyphicon glyphicon-refresh"></span> '.Yii::t('plugin-version', 'button_provider_change'), ArrayHelper::merge(['select-provider'], $this->context->urlParams), [
					'class' => 'btn btn-default btn-lg'
				]) ?>
			<?php } ?>
		</div>
	</div>

    <?php ActiveForm::end(); ?>

</div>
