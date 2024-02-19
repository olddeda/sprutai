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
use common\modules\base\extensions\phoneInput\PhoneInput;
use common\modules\base\helpers\enum\Status;

use common\modules\seo\widgets\SeoFormWidget;

use common\modules\tag\models\Tag;

/* @var $this yii\web\View */
/* @var $model common\modules\company\models\Company */
/* @var $form yii\widgets\ActiveForm */

?>

<?= $this->render('@common/modules/content/views/_contentbuilder_js.php', [
	'model' => $model,
	'validateTag' => false,
	'validateSpecial' => false,
]) ?>

<div class="company-form">
	
	<?php $form = ActiveForm::begin(['id' => 'form-content', 'options' => ['enctype' => 'multipart/form-data']]); ?>

	<div class="grid">
		<div class="col width-350">
			<div class="panel panel-default">
				<div class="panel-body">

					<fieldset>
						<legend><?= Yii::t('company', 'header_general') ?></legend>

						<div>
							<label><?= Yii::t('company', 'field_logo') ?></label>
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
							
							<?= $form->field($model, 'descr')->textarea(['rows' => 7, 'doubleEncode' => false]) ?>
							
							<?= $form->field($model, 'site') ?>
							
							<?= $form->field($model, 'email') ?>
							
							<?= $form->field($model, 'phone')->widget(PhoneInput::class, [
								'auto' => true,
								'defaultOptions' => [
									'class' => 'form-control',
								],
								'jsOptions' => [
									'nationalMode' => false,
									'autoHideDialCode' => false,
									'preferredCountries' => ['ru'],
								],
							]) ?>
							
							<?= $form->field($model, 'telegram', [
								'template' => '{beginLabel}{labelTitle}{endLabel}<div class="input-group"><span class="input-group-addon">t.me/</span>{input}</div>{error}{hint}'
							]) ?>
							
							<?= $form->field($model, 'instagram', [
								'template' => '{beginLabel}{labelTitle}{endLabel}<div class="input-group"><span class="input-group-addon">instagram.com/</span>{input}</div>{error}{hint}'
							]) ?>
							
							<?= $form->field($model, 'facebook', [
								'template' => '{beginLabel}{labelTitle}{endLabel}<div class="input-group"><span class="input-group-addon">facebook.com/</span>{input}</div>{error}{hint}'
							]) ?>
							
							<?= $form->field($model, 'vk', [
								'template' => '{beginLabel}{labelTitle}{endLabel}<div class="input-group"><span class="input-group-addon">vk.com/</span>{input}</div>{error}{hint}'
							]) ?>
							
							<?= $form->field($model, 'ok', [
								'template' => '{beginLabel}{labelTitle}{endLabel}<div class="input-group"><span class="input-group-addon">ok.ru/group/</span>{input}</div>{error}{hint}'
							]) ?>
							
							<?php if (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor()) { ?>
							<?= $form->field($model, 'tag_id')->widget(Select2::class, [
								'items' => Tag::listData(),
								'clientOptions' => [
									'hideSearch' => false,
									'allowClear' => true,
									'placeholder' => Yii::t('company', 'placeholder_tag_id'),
								]
							]) ?>
							
							<?php } ?>
							
						</div>
						
					</fieldset>
					
					<?php if (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor()) { ?>
					<fieldset>
						<legend><?= Yii::t('company', 'header_type') ?></legend>
						
						<?php foreach (['is_vendor', 'is_integrator', 'is_shop'] as $field) { ?>
						<?= $form->field($model, $field)->checkbox() ?>
						<?php } ?>
					</fieldset>
					
					<fieldset>
						<legend><?= Yii::t('company', 'header_other') ?></legend>
						
						<?= $form->field($model, 'status')->widget(Select2::class, [
							'items' => Status::listData(),
							'clientOptions' => [
								'hideSearch' => true,
							]
						]) ?>
					</fieldset>

					<fieldset>
						<legend><?= Yii::t('company', 'header_seo') ?></legend>
						
						<?= SeoFormWidget::widget([
							'model' => $model,
							'form' => $form,
						]); ?>
					</fieldset>
					
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
									'doubleEncode' => false,
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
							<?php $content = '<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')); ?>
							<?= Html::button($content, [
								'id' => 'button-content-submit',
								'class' => 'btn btn-primary btn-lg',
								'data-title-original' => $content,
								'data-title-wait' => '<span class="glyphicon glyphicon-time"></span> '.Yii::t('base', 'button_wait'),
							]) ?>
							<?php if (Yii::$app->user->can('company.default.index')) { ?>
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