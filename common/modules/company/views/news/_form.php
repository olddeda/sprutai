<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\selectize\Selectize;
use common\modules\base\extensions\datetimepicker\DateTimePicker;
use common\modules\base\extensions\contentbuilder\ContentBuilder;
use common\modules\base\helpers\enum\Boolean;

use common\modules\rbac\helpers\enum\Role;

use common\modules\seo\widgets\SeoFormWidget;

use common\modules\media\helpers\enum\Mode;

use common\modules\tag\models\Tag;
use common\modules\tag\helpers\enum\Type;

use common\modules\content\helpers\enum\Status;

/* @var $this yii\web\View */
/* @var $company common\modules\company\models\Company */
/* @var $model common\modules\content\models\News */
/* @var $form yii\widgets\ActiveForm */
?>

<?= $this->render('@common/modules/content/views/_contentbuilder_js.php', [
    'model' => $model,
	'validateTag' => true,
	'validateSpecial' => true,
]) ?>

<?php

$js =<<<JS

	$('#news-pinned').change(function() {
		var val = parseInt($(this).val());
		if (val)
			$('#div-pinned-sequence').slideDown();
		else
			$('#div-pinned-sequence').slideUp();
	});

JS;
$this->registerJs($js);
?>

<div class="content-news-form">

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
	                    <?= $form->field($model, 'descr')->textarea(['rows' => 7, 'value' => html_entity_decode($model->descr)]) ?>
                    </div>
	
	                <?php if (Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) { ?>
                        <div class="row margin-top-20">
                            <div class="col-md-12">
                                
                                <fieldset>
                                    <legend><?= Yii::t('content-news', 'header_general') ?></legend>
					
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
	
									<?= $form->field($model, 'datetime')->widget(DateTimePicker::class, [
										'template' => '{input}{button}{reset}',
										'pickButtonIcon' => 'glyphicon glyphicon-calendar',
										'clientOptions' => [
											'autoclose' => true,
											'format' => 'dd-mm-yyyy HH:ii',
											'todayBtn' => true,
											'minView' => 0,
										],
									]);?>
                                
                                </fieldset>
                            </div>
                        </div>

                        <div class="row margin-top-30">
                            <div class="col-md-12">
                                <fieldset>
                                    <legend><?= Yii::t('content-news', 'header_other') ?></legend>
	
	                                <?= $form->field($model, 'pinned')->widget(Select2::class, [
		                                'items' => Boolean::listData(),
		                                'clientOptions' => [
			                                'hideSearch' => true,
		                                ]
	                                ]) ?>
	                                
	                                <?= Html::beginTag('div', ['id' => 'div-pinned-sequence' , 'style' => ($model->pinned ? 'display:block' : 'display:none')]) ?>

	                                <div id="div-pinned-sequence">
		                                <?= $form->field($model, 'pinned_sequence') ?>
	                                </div>
	                                
	                                <?= Html::endTag('div') ?>
					
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

                                    <?= $form->field($model, 'change_catalog_links')->checkbox() ?>
	                               
                                </fieldset>

                                <fieldset>
                                    <legend><?= Yii::t('content-news', 'header_seo') ?></legend>
					
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
					                ['bold', 'italic', 'formatting', 'textsettings', 'color', 'font', 'formatPara', 'align', 'list', 'table', 'image', 'createLink', 'unlink', 'icon', 'tags', 'removeFormat', 'html'] :
					                ['bold', 'italic', 'formatting', 'align', 'list', 'table', 'createLink', 'unlink', 'icon', 'tags', 'removeFormat'],
				                'content' => $model->text,
				                ''
			                ],
		                ])->label(false) ?>

                        <div class="form-group margin-top-40 align-center">
                            <div class="btn-group btn-group-lg">
				                <?= Html::button('<span class="fa fa-save"></span> '.Yii::t('base', 'button_draft'), [
					                'name' => 'draft',
					                'id' => 'button-content-draft',
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
			                <?php if (Yii::$app->user->can('content.news.index')) { ?>
				                <?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index', 'company_id' => $company->id], [
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

