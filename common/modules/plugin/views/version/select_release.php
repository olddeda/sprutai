<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\select2\Select2;

/* @var $this yii\web\View */
/* @var $plugin common\modules\plugin\models\Plugin */
/* @var $model common\modules\plugin\models\Version */
/* @var $id integer */
/* @var $releases array */
/* @var $disabledOptions array */
/* @var $isExists boolean */

$this->title = Yii::t('plugin-version', 'title_select_release');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['/plugin/default/index']];
$this->params['breadcrumbs'][] = ['label' => $plugin->title, 'url' => ['/plugin/default/view', 'id' => $plugin->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin-version', 'title'), 'url' => ['index', 'plugin_id' => $plugin->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="plugin-version-select-release">
	
	<?php $form = ActiveForm::begin([
		'id' => 'version-form',
		'enableAjaxValidation' => false,
	]); ?>
	
	<?php if (count($releases) && !$isExists) { ?>
	<fieldset>
		<legend><?= Yii::t('plugin-version', 'header_select_release') ?></legend>
		
		<?= $form->field($model->repository, 'tag')->widget(Select2::class, [
			'items' => ArrayHelper::map($releases, 'tag', 'tag'),
			'options' => [
				'options' => $disabledOptions,
			],
			'clientOptions' => [
				'hideSearch' => true,
			]
		])->label(false)->hint(Yii::t('plugin-version', 'hint_create_new_release', ['url' => $model->repository->getCreateReleaseUrl()])) ?>
	</fieldset>
	
	<?php } else { ?>
	
	<fieldset>
		<legend><?= Yii::t('plugin-version', 'header_select_release_empty') ?></legend>
		
		<label class="empty"><?= Yii::t('plugin-version', 'error_empty_releases', ['url' => $model->repository->getCreateReleaseUrl()]) ?></label>
	</fieldset>
	<?php } ?>

	<div class="form-group margin-top-30">
		<div class="pull-left">
			<?php if (count($releases) && !$isExists) { ?>
				<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.Yii::t('base', 'button_next'), [
					'class' => 'btn btn-primary btn-lg'
				]) ?>
			<?php } ?>
		</div>
		<div class="pull-right">
			<?php if (Yii::$app->user->can('plugin.version.select-repository')) { ?>
				<?= Html::a('<span class="glyphicon glyphicon-refresh"></span> '.Yii::t('plugin-version', 'button_repository_change'), ArrayHelper::merge(['select-repository'], $this->context->urlParams), [
					'class' => 'btn btn-default btn-lg'
				]) ?>
			<?php } ?>
			<?php if (Yii::$app->user->can('plugin.version.select-provider')) { ?>
				<?= Html::a('<span class="glyphicon glyphicon-refresh"></span> '.Yii::t('plugin-version', 'button_provider_change'), ArrayHelper::merge(['select-provider'], $this->context->urlParams), [
					'class' => 'btn btn-default btn-lg'
				]) ?>
			<?php } ?>
		</div>
	</div>
	
	<?php ActiveForm::end(); ?>


</div>