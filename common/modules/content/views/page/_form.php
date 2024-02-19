<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

use common\modules\base\helpers\enum\Status;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\selectize\Selectize;
use common\modules\base\extensions\datetimepicker\DateTimePicker;
use common\modules\base\extensions\contentbuilder\ContentBuilder;

use common\modules\seo\widgets\SeoFormWidget;

use common\modules\rbac\helpers\enum\Role;
use common\modules\media\helpers\enum\Mode;

use common\modules\tag\models\Tag;
use common\modules\tag\helpers\enum\Type;

use common\modules\content\models\Page;
use common\modules\content\helpers\enum\PageType;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Page */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$js = <<<JS
	$('#page-page_type').change(function() {
		var val = parseInt($(this).val());
		if (!val) {
			$('#div-page-text').slideDown();
			$('#div-page-path').slideUp();
		}
		else {
			$('#div-page-text').slideUp();
			$('#div-page-path').slideDown();
		}
	});
JS;
$this->registerJs($js, View::POS_END);
?>

<?= $this->render('../_contentbuilder_js.php', [
	'model' => $model,
	'validateTag' => false,
	'validateSpecial' => false,
]) ?>

<div class="page-form">
	
	<?php $form = ActiveForm::begin(['id' => 'form-content', 'options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="grid">
        <div class="col width-350">

            <div class="panel panel-default">
                <div class="panel-body">
	
					<?= $model->image->uploaderImageSlim([
						'settings' => [
							'size' => [
								'width' => 1000,
								'height' => 600,
							],
						],
						'format' => Mode::CROP_CENTER,
					]); ?>

                    <div class="margin-top-20">
						<?= $form->field($model, 'descr')->textarea(['rows' => 7]) ?>
						
						<?= $form->field($model, 'content_id')->widget(Select2::class, [
							'items' => Page::treeListData(),
							'clientOptions' => [
								'hideSearch' => true,
								'options' => [
									$model->id => ['disabled' => true]
								],
							]
						]) ?>
						
						<?= $form->field($model, 'page_type')->widget(Select2::class, [
							'items' => PageType::listData(),
							'clientOptions' => [
								'hideSearch' => true,
							]
						]) ?>

                        <div id="div-page-path" style="<?= ($model->page_type == PageType::TEXT ? 'display:none' : '') ?>">
		                    <?= $form->field($model, 'page_path', [
			                    'template' => '{label}<div class="input-group">{input}<span class="input-group-addon">.php</span></div>{hint}{error}',
		                    ])->textInput(['maxlength' => true]) ?>
                        </div>
                        
                    </div>
					
					<?php if (Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) { ?>
                        <div class="row margin-top-20">
                            <div class="col-md-12">

                                <fieldset>
                                    <legend><?= Yii::t('content-page', 'header_general') ?></legend>
									
									<?= $form->field($model, 'tags_ids')->widget(Selectize::class, [
										'items' => Tag::listDataType('id', 'title', 'title', [Type::NONE]),
										'pluginOptions' => [
											'plugins' => ['remove_button'],
											'persist' => false,
											'createOnBlur' => false,
											'create' => false,
										],
										'options' => [
											'multiple' => true,
											'class' => 'form-control',
										]
									]); ?>

                                </fieldset>
                            </div>
                        </div>

                        <div class="row margin-top-30">
                            <div class="col-md-12">
                                <fieldset>
                                    <legend><?= Yii::t('content-page', 'header_other') ?></legend>
									
									<?= $form->field($model, 'status')->widget(Select2::class, [
										'items' => Status::listData(),
										'clientOptions' => [
											'hideSearch' => true,
										]
									]) ?>
                                </fieldset>

                                <fieldset>
                                    <legend><?= Yii::t('content-page', 'header_seo') ?></legend>
									
									<?= SeoFormWidget::widget([
										'model' => $model,
										'form' => $form,
									]); ?>
                                </fieldset>
                            </div>
                        </div>
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
									'value' => html_entity_decode($model->title),
								])->label(false)->error(false) ?>
                            </div>
                        </div>
						
						<?= $form->field($model, 'text')->widget(ContentBuilder::class, [
							'pluginOptions' => [
								'sourceEditor' => Yii::$app->user->getIsAdmin(),
								'buttons' => Yii::$app->user->getIsAdmin() ?
									['bold', 'italic', 'strikethrough', 'formatting', 'textsettings', 'color', 'font', 'formatPara', 'align', 'list', 'table', 'image', 'createLink', 'unlink', 'icon', 'tags', 'removeFormat', 'html'] :
									['bold', 'italic', 'strikethrough', 'formatting', 'align', 'list', 'table', 'createLink', 'unlink', 'icon', 'tags', 'removeFormat'],
								'content' => $model->text,
								''
							],
						])->label(false) ?>

                        <div class="form-group margin-top-40 align-center">
                            <div class="btn-group btn-group-lg">
                                <?php $content = '<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')); ?>
                                <?= Html::button($content, [
                                    'id' => 'button-content-submit',
                                    'class' => 'btn btn-primary btn-lg',
                                    'data-title-original' => $content,
                                    'data-title-wait' => '<span class="glyphicon glyphicon-time"></span> '.Yii::t('base', 'button_wait'),
                                ]) ?>
                            </div>
							<?php if (Yii::$app->user->can('content.page.index')) { ?>
								<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
									'id' => 'button-content-back',
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
