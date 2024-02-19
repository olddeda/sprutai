<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2015 - 2017
 * @package yii2-dynagrid
 * @version 1.4.5
 */


use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\sortable\Sortable;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use kartik\dynagrid\Module;
use kartik\base\Config;
use yii\bootstrap\Modal;

/**
 * @var yii\web\View $this
 * @var kartik\dynagrid\models\DynaGridConfig $model
 * @var ActiveForm $form
 * @var boolean $allowPageSetting
 * @var boolean $allowThemeSetting
 * @var boolean $allowFilterSetting
 * @var boolean $allowSortSetting
 * @var array $toggleButtonGrid
 */

$visibleColumns = [];
foreach ($model->visibleColumns as $row) {
	if (!isset($row['disabled']) || !$row['disabled'])
		$visibleColumns[] = $row;
}
$hiddenColumns = [];
foreach ($model->hiddenColumns as $row) {
	if (!isset($row['disabled']) || !$row['disabled'])
		$hiddenColumns[] = $row;
}
$options1 = ArrayHelper::merge($model->widgetOptions, [
	'items' => $visibleColumns,
	'connected' => true,
	'options' => ['class' => 'sortable-visible'],
]);
$options2 = ArrayHelper::merge($model->widgetOptions, [
	'items' => $hiddenColumns,
	'connected' => true,
	'options' => ['class' => 'sortable-hidden'],
]);

$module = Config::getModule($moduleId, Module::class);
$cols = (int) $allowPageSetting + (int) $allowThemeSetting + (int) $allowFilterSetting + (int) $allowSortSetting;
$col = $cols == 0 ? 0 : 12 / $cols;

$model->footer = '
<button title="'.Yii::t('base-dynagrid', 'button_save_title').'" class="btn btn-primary dynagrid-submit" type="button"><span class="glyphicon glyphicon-ok"></span> '.Yii::t('base-dynagrid', 'button_save').'</button>
<button title="'.Yii::t('base-dynagrid', 'button_reset_title').'" class="btn btn-default dynagrid-reset" type="reset"><span class="glyphicon glyphicon-repeat"></span> '.Yii::t('base-dynagrid', 'button_reset').'</button>';

?>


<?php
Modal::begin([
	'header' => '<h4 class="modal-title">'.Yii::t('base-dynagrid', 'title').'</h4>',
	'footer' => $model->footer,
	'toggleButton' => $toggleButtonGrid,
	'size' => Modal::SIZE_LARGE,
	'options' => ['id' => $id],
]);
?>

<?php $form = ActiveForm::begin(['options' => ['data-pjax' => false]]); ?>
<div class="dynagrid-config-form">
	<?php if ($col != 0): ?>
		<div class="row">
			<?php if ($allowPageSetting): ?>
				<div class="col-sm-<?= $col ?>">
					<?= $form->field($model, 'pageSize')->textInput(['class' => 'form-control'])->hint(Yii::t('kvdynagrid', "Integer between {min} to {max}", [
							'min' => $module->minPageSize,
							'max' => $module->maxPageSize,
						])) ?>
				</div>
			<?php endif; ?>
			<?php if ($allowThemeSetting): ?>
				<div class="col-sm-<?= $col ?>">
					<?= $form->field($model, 'theme')->widget(Select2::class, [
						'data' => $model->themeList,
						'options' => ['placeholder' => Yii::t('kvdynagrid', 'Select a theme...')],
						'pluginOptions' => ['allowClear' => true],
					])->hint(Yii::t('kvdynagrid', 'Select theme to style grid')); ?>
				</div>
			<?php else: ?>
				<?= Html::activeHiddenInput($model, 'theme') ?>
			<?php endif; ?>
			<?php if ($allowFilterSetting): ?>
				<div class="col-sm-<?= $col ?>">
					<?= $form->field($model, 'filterId')->widget(Select2::class, [
						'data' => $model->filterList,
						'options' => ['placeholder' => Yii::t('kvdynagrid', 'Select a filter...')],
						'pluginOptions' => ['allowClear' => true],
					])->hint(Yii::t('kvdynagrid', 'Set default grid filter criteria')) ?>
				</div>
			<?php endif; ?>
			<?php if ($allowSortSetting): ?>
				<div class="col-sm-<?= $col ?>">
					<?= $form->field($model, 'sortId')->widget(Select2::class, [
						'data' => $model->sortList,
						'options' => ['placeholder' => Yii::t('kvdynagrid', 'Select a sort...')],
						'pluginOptions' => ['allowClear' => true],
					])->hint(Yii::t('kvdynagrid', 'Set default grid sort criteria')) ?>
				</div>
			<?php endif; ?>
			<?= Html::hiddenInput('deleteFlag', 0) ?>
		</div>
	<?php endif; ?>
	<div class="row">
		<div class="col-sm-5">
			<fieldset>
				<legend><?= Yii::t('base-dynagrid', 'header_columns_visible') ?></legend>
				<?= Sortable::widget($options1); ?>
			</fieldset>
		</div>
		<div class="col-sm-2 text-center">
			<div class="dynagrid-sortable-separator"><i class="glyphicon glyphicon-resize-horizontal"></i></div>
		</div>
		<div class="col-sm-5">
			<fieldset>
				<legend><?= Yii::t('base-dynagrid', 'header_columns_hidden') ?></legend>
				<?= Sortable::widget($options2); ?>
			</fieldset>
		</div>
	</div>
	
	<?= Html::hiddenInput($model->id, 1) ?>
	<?= Html::hiddenInput('visibleKeys') ?>
	<?php ActiveForm::end(); ?>
</div>
<?php Modal::end(); ?>