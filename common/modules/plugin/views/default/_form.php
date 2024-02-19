<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Json;

use kartik\number\NumberControl;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\selectize\Selectize;
use common\modules\base\extensions\contentbuilder\ContentBuilder;

use common\modules\tag\models\Tag;
use common\modules\tag\helpers\enum\Type;

use common\modules\rbac\helpers\enum\Role;

use common\modules\media\helpers\enum\Mode;

use common\modules\seo\widgets\SeoFormWidget;

use common\modules\payment\models\PaymentType;

use common\modules\content\helpers\enum\Status;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\plugin */
/* @var $form yii\widgets\ActiveForm */

?>

<?= $this->render('@common/modules/content/views/_contentbuilder_js.php', [
	'model' => $model,
	'validateTag' => true,
	'validateSpecial' => true,
]) ?>

<?php
$typesIds = Json::encode($model->payment_types_ids);

$js =<<<JS
	var typesIds = $typesIds;

	$('#plugin-payment_types_ids').change(function() {
		var val = $(this).val();
		var typesIdsNew = [];
		$.each(val, function(key, val) {
			typesIdsNew.push(parseInt(val));
		});
		
		if (_.isEqual(typesIds.sort(), typesIdsNew.sort())) {
			$('#button-types-apply').slideUp();
        }
        else {
			$('#button-types-apply').slideDown();
        }
	});
	
	for (key in typesIds) {
		var typeId = typesIds[key];
		
		$('#payment_type_module_' + typeId + '-price_free').change(function() {
			var obj = $('#ptm_' + typeId);
			var val = $(this).is(':checked');
			if (val) {
				obj.addClass('hidden');
			}
			else {
				obj.removeClass('hidden');
			}
		});
	}
	
	$('#button-types-apply').click(function() {
		$('#types-apply').val(1);
		contentBuilderSave($(this))
	});
JS;
$this->registerJs($js);
?>

<div class="plugin-form">
	
	<?php $form = ActiveForm::begin(['id' => 'form-content', 'options' => ['enctype' => 'multipart/form-data']]); ?>

	<div class="grid">
		<div class="col width-350">
			<div class="panel panel-default">
				<div class="panel-body">

					<fieldset>
						<legend><?= Yii::t('plugin', 'header_general') ?></legend>
						
						<div>
							<label><?= Yii::t('plugin', 'field_logo') ?></label>
							<?= $model->logo->uploaderImageSlim([
								'settings' => [
									'size' => [
										'width' => 600,
										'height' => 600,
									],
								],
							]); ?>
						</div>
						
						<div class="margin-top-20">
							<label><?= Yii::t('plugin', 'field_image') ?></label>
							<?= $model->background->uploaderImageSlim([
								'settings' => [
									'size' => [
										'width' => 1000,
										'height' => 600,
									],
								],
							]); ?>
						</div>

						<div class="margin-top-20">
							<?= $form->field($model, 'descr')->textarea(['rows' => 7]) ?>
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
								'class' => 'form-control',
							]
						]); ?>
						
					</fieldset>
					
					<?php foreach ($model->paymentTypeModule as $ptm) {?>
						<fieldset>
							<legend><?= $ptm->type->title ?></legend>
							
							<div id="ptm_<?= $ptm->type->id ?>" class="<?= $ptm->price_free ? 'hidden' : '' ?>">
								<?= $form->field($ptm, 'price')->widget(NumberControl::class, [
									'options' => [
										'value' => (int)$ptm->price,
									],
									'maskedInputOptions' => [
										'suffix' => ' ₽',
										'digits' => 0,
										'groupSeparator' => ' ',
										'rightAlign' => false,
									],
									'displayOptions' => [
										'id' => 'payment_type_module_'.$ptm->type->id.'-price',
										'name' => 'PaymentTypeModules['.$ptm->type->id.'][price]',
										'placeholder' => Yii::t('payment-type-module', 'placeholder_price'),
									],
								]) ?>
								
								<?= $form->field($ptm, 'price_fixed')->checkbox([
									'id' => 'payment_type_module_'.$ptm->type->id.'-price_fixed',
									'name' => 'PaymentTypeModules['.$ptm->type->id.'][price_fixed]',
								]) ?>
							</div>
							
							<?= $form->field($ptm, 'price_free')->checkbox([
								'id' => 'payment_type_module_'.$ptm->type->id.'-price_free',
								'name' => 'PaymentTypeModules['.$ptm->type->id.'][price_free]',
							]) ?>

						</fieldset>
					<?php } ?>
					
					<?php if (Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) { ?>
					<fieldset>
						<legend><?= Yii::t('plugin', 'header_other') ?></legend>
						
						<?= $form->field($model, 'status')->widget(Select2::class, [
							'items' => Status::listData(),
							'clientOptions' => [
								'hideSearch' => true,
							]
						]) ?>
						
						<?= $form->field($model, 'type')->widget(Select2::class, [
							'items' => \common\modules\content\helpers\enum\Type::listData([], [
								\common\modules\content\helpers\enum\Type::PAGE,
								\common\modules\content\helpers\enum\Type::INSTRUCTION,
							]),
							'clientOptions' => [
								'hideSearch' => true,
							]
						]) ?>
						
					</fieldset>

					<fieldset>
						<legend><?= Yii::t('plugin', 'header_seo') ?></legend>
						
						<?= SeoFormWidget::widget([
							'model' => $model,
							'form' => $form,
						]); ?>
					</fieldset>
					<?php } else { ?>
					<?= $form->field($model, 'status')->hiddenInput()->label(false) ?>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="col width-auto">
			<div class="panel panel-default">
				<div class="panel-body">

					<div class="container container-fluid is-container contentbuilder">

						<div class="row parent-focused">
							<div class="col-md-12">
								<?= $form->field($model, 'title', ['options' => ['class' => 'content-editable contentbuilder-title', 'tabindex' => 0]])->textarea([
									'placeholder' => Yii::t('content', 'placeholder_title'),
									'autocomplete' => 'off',
								])->label(false)->error(false) ?>
							</div>
						</div>
						
						<?= $form->field($model, 'text')->widget(ContentBuilder::class, [
							'pluginOptions' => [
								'sourceEditor' => Yii::$app->user->getIsAdmin(),
								'buttons' => Yii::$app->user->getIsAdmin() ?
									['bold', 'italic', 'formatting', 'textsettings', 'color', 'font', 'formatPara', 'align', 'list', 'table', 'image', 'createLink', 'unlink', 'icon', 'tags', 'removeFormat', 'html'] :
									['bold', 'italic', 'formatting', 'align', 'list', 'table', 'createLink', 'unlink', 'icon', 'tags', 'removeFormat'],
								'content' => $model->text,
								''
							],
						])->label(false) ?>

						<div class="form-group margin-top-40 align-center">
							<div class="btn-group btn-group-lg">
								<?= Html::button('<span class="fa fa-save"></span> '.Yii::t('base', 'button_draft'), [
									'id' => 'button-content-draft',
									'name' => 'draft',
									'class' => 'btn btn-default btn-lg',
									'data-title-original' => '<span class="fa fa-save"></span> '.Yii::t('base', 'button_draft'),
									'data-title-wait' => '<span class="glyphicon glyphicon-time"></span> '.Yii::t('base', 'button_wait'),
								]) ?>
								
								<?php if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) { ?>
									<?= Html::button('<span class="glyphicon glyphicon-ok"></span> '.Yii::t('base', 'button_moderate'), [
										'id' => 'button-content-moderate',
										'name' => 'moderate',
										'class' => 'btn btn-primary btn-lg',
										'data-title-original' => '<span class="glyphicon glyphicon-ok"></span> '.Yii::t('base', 'button_moderate'),
										'data-title-wait' => '<span class="glyphicon glyphicon-time"></span> '.Yii::t('base', 'button_wait'),
									]) ?>
								<?php } else { ?>
									<?php $content = '<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')); ?>
									<?= Html::button($content, [
										'id' => 'button-content-submit',
										'class' => 'btn btn-primary btn-lg',
										'data-title-original' => $content,
										'data-title-wait' => '<span class="glyphicon glyphicon-time"></span> '.Yii::t('base', 'button_wait'),
									]) ?>
								<?php } ?>
							</div>
							<?php if (Yii::$app->user->can('plugin.default.index')) { ?>
								<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
									'class' => 'btn btn-default btn-lg'
								]) ?>
							<?php } ?>
						</div>
						
					</div>
					
				</div>
			</div>

		</div>
	</div>

    <?php ActiveForm::end(); ?>

</div>
