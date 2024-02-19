<?php

use common\modules\content\helpers\enum\Status;
use common\modules\seo\widgets\SeoFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\helpers\enum\Boolean;
use common\modules\base\extensions\datetimepicker\DateTimePicker;
use common\modules\base\extensions\selectize\Selectize;

use common\modules\rbac\helpers\enum\Role;

use common\modules\tag\helpers\enum\Type;
use common\modules\tag\models\Tag;

/* @var $this yii\web\View */
/* @var $company common\modules\company\models\Company */
/* @var $model common\modules\company\models\CompanyDiscount */
/* @var $form yii\widgets\ActiveForm */
?>

<?php

$js =<<<JS
$('#companydiscount-infinitely').change(function() {
	var val = $(this).is(':checked');
	if (val)
		$('#company-discount-date-block').slideUp();
	else
		$('#company-discount-date-block').slideDown();
});
JS;
$this->registerJs($js);
?>

<div class="company-discount-form">

	<?php $form = ActiveForm::begin([
		'id' => 'company-discount-form',
		'enableAjaxValidation' => false,
		'enableClientValidation' => true,
	]); ?>

	<div class="row margin-top-0">
		<div class="col-md-12">

			<fieldset>
				<legend><?= Yii::t('company-discount', 'header_general') ?></legend>

				<div class="row">
					<div class="col-md-6">
						<?= $form->field($model, 'promocode') ?>
					</div>
					<div class="col-md-3">
						<?= $form->field($model, 'discount', [
							'template' => '{label}<div class="input-group">{input}<span class="input-group-addon">%</span></div>{error}{hint}'
						]) ?>
					</div>
					<div class="col-md-3">
                        <?= $form->field($model, 'discount_to', [
                            'template' => '{label}<div class="input-group">{input}<span class="input-group-addon">%</span></div>{error}{hint}'
                        ]) ?>
					</div>
				</div>
				
				<?= $form->field($model, 'infinitely')->checkbox() ?>

				<div id="company-discount-date-block" class="row" style="display: <?= $model->infinitely ? 'none' : 'block' ?>">
					<div class="col-md-6">
						<?= $form->field($model, 'date_start')->widget(DateTimePicker::class, [
							'template' => '{input}{button}{reset}',
							'pickButtonIcon' => 'glyphicon glyphicon-calendar',
							'clientOptions' => [
								'autoclose' => true,
								'format' => 'dd-mm-yyyy',
								'todayBtn' => true,
								'minView' => 2,
							],
						]);?>
					</div>
					<div class="col-md-6">
						<?= $form->field($model, 'date_end')->widget(DateTimePicker::class, [
							'template' => '{input}{button}{reset}',
							'pickButtonIcon' => 'glyphicon glyphicon-calendar',
							'clientOptions' => [
								'autoclose' => true,
								'format' => 'dd-mm-yyyy',
								'todayBtn' => true,
								'minView' => 2,
							],
						]);?>
					</div>
				</div>
				
				<?= $form->field($model, 'tags_ids')->widget(Selectize::class, [
					'items' => Tag::listDataType('id', 'title', 'title', [Type::NONE, Type::SYSTEM]),
					'pluginOptions' => [
						'plugins' => ['remove_button'],
						'persist' => false,
						'createOnBlur' => false,
						'create' => false,
						'valueField' => 'id',
						'searchField' => 'title',
						'options' => Tag::listWithColorsType([Type::NONE, Type::SYSTEM]),
						'render' => [
							'option' => new \yii\web\JsExpression('function(data, escape) { return \'<div class="option \' + escape(data.color) + \'">\' + escape(data.title) + \'</div>\'  }'),
							'item' => new \yii\web\JsExpression('function(data, escape) { return \'<div class="item \' + escape(data.color) + \'" data-value="\' + escape(data.value) + \'">\' + escape(data.title) + \'</div>\'  }'),
						],
					],
					'options' => [
						'multiple' => true,
						'class' => 'form-control required',
					]
				]); ?>
				
				<?= $form->field($model, 'descr')->textarea([
					'rows' => 3,
				]) ?>

			</fieldset>
			
			<?php if (Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) { ?>
			<fieldset>
				<legend><?= Yii::t('company-discount', 'header_other') ?></legend>
				
				<?= $form->field($model, 'status')->widget(Select2::class, [
					'items' => Status::listData(),
					'clientOptions' => [
						'hideSearch' => true,
					]
				]) ?>

			</fieldset>
			<?php } else { ?>
			<?= $form->field($model, 'status')->hiddenInput()->label(false) ?>
			<?php } ?>
			
		</div>
	</div>

	<div class="form-group margin-top-0">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
		<?php if (Yii::$app->user->can('company.discount.index')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index', 'company_id' => $company->id], [
				'class' => 'btn btn-default btn-lg'
			]) ?>
		<?php } ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
