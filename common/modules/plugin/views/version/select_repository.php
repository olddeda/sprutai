<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\select2\Select2;

/* @var $this yii\web\View */
/* @var $plugin common\modules\plugin\models\Plugin */
/* @var $model common\modules\plugin\models\Version */
/* @var $id integer */
/* @var $repositories array */

$this->title = Yii::t('plugin-version', 'title_select_repository');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['/plugin/default/index']];
$this->params['breadcrumbs'][] = ['label' => $plugin->title, 'url' => ['/plugin/default/view', 'id' => $plugin->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin-version', 'title'), 'url' => ['index', 'plugin_id' => $plugin->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="plugin-version-select-repository">
	
	<?php $form = ActiveForm::begin([
		'id' => 'version-form',
		'enableAjaxValidation' => false,
	]); ?>
	
	
	<?php if (count($repositories)) { ?>
	
	<fieldset>
		<legend><?= Yii::t('plugin-version', 'header_select_repository') ?></legend>
		
		<?= $form->field($model->repository, 'name')->widget(Select2::class, [
			'items' => ArrayHelper::map($repositories, 'name', 'name'),
			'clientOptions' => [
				'hideSearch' => true,
			]
		])->label(false)->hint(Yii::t('plugin-version', 'hint_create_new_repository', ['url' => $model->repository->getCreateRepositoryUrl()])) ?>
	</fieldset>
	
	<?php } else { ?>
	
	<fieldset>
		<legend><?= Yii::t('plugin-version', 'header_select_repository_empty') ?></legend>
		
		<label class="empty"><?= Yii::t('plugin-version', 'error_empty_repositories', ['url' => $model->repository->getCreateRepositoryUrl()]) ?></label>
	</fieldset>
	
	<?php } ?>

	<div class="form-group margin-top-30">
		<div class="pull-left">
			<?php if (count($repositories)) { ?>
				<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.Yii::t('base', 'button_next'), [
					'class' => 'btn btn-primary btn-lg'
				]) ?>
			<?php } ?>
		</div>
		<div class="pull-right">
			<?php if (Yii::$app->user->can('plugin.version.select-provider')) { ?>
				<?= Html::a('<span class="glyphicon glyphicon-refresh"></span> '.Yii::t('plugin-version', 'button_provider_change'), ArrayHelper::merge(['select-provider'], $this->context->urlParams), [
					'class' => 'btn btn-default btn-lg'
				]) ?>
			<?php } ?>
		</div>
	</div>
	
	<?php ActiveForm::end(); ?>

</div>