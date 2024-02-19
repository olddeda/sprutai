<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

use common\modules\base\extensions\select2\Select2;

use common\modules\plugin\helpers\enum\RepositoryProvider;

/* @var $this yii\web\View */
/* @var $plugin common\modules\plugin\models\Plugin */
/* @var $model common\modules\plugin\models\Version */
/* @var $id integer */

$this->title = Yii::t('plugin-version', 'title_select_provider');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['/plugin/default/index']];
$this->params['breadcrumbs'][] = ['label' => $plugin->title, 'url' => ['/plugin/default/view', 'id' => $plugin->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin-version', 'title'), 'url' => ['index', 'plugin_id' => $plugin->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="plugin-version-select-provider">
	
	<?php $form = ActiveForm::begin([
		'id' => 'version-form',
		'enableAjaxValidation' => false,
	]); ?>

	<fieldset>
		<legend><?= Yii::t('plugin-version', 'header_select_provider') ?></legend>
		
		<?= $form->field($model->repository, 'provider')->widget(Select2::class, [
			'items' => RepositoryProvider::listData(),
			'clientOptions' => [
				'hideSearch' => true,
			]
		])->label(false) ?>
	</fieldset>

	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.Yii::t('base', 'button_next'), [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>
	
	<?php ActiveForm::end(); ?>
	
</div>