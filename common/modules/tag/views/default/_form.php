<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

use common\modules\base\helpers\enum\Status;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\selectize\Selectize;

use common\modules\media\helpers\enum\Mode;

use common\modules\seo\widgets\SeoFormWidget;

use common\modules\tag\models\Tag;
use common\modules\tag\helpers\enum\Type;

/* @var $this yii\web\View */
/* @var $model common\modules\tag\models\Tag */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="content-news-form">
	
	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
	
	<fieldset>
		<legend><?= Yii::t('tag', 'header_general') ?></legend>
		
		<div class="grid">
			<div class="col width-350">
				<?= $model->image->uploaderImageSlim([
					'settings' => [
						'size' => [
							'width' => 1000,
							'height' => 1000,
						],
					],
					'format' => Mode::CROP_CENTER,
				]); ?>
			</div>
			<div class="col width-auto">
				<?= Html::activeLabel($model, 'type') ?>
                <?php foreach (Tag::attributeTypes() as $field => $val) { ?>
                    <?= $form->field($model, $field)->checkbox([
                    	//'disabled' => ($val == Type::NONE),
	                ]) ?>
                <?php } ?>
				
				<?= $form->field($model, 'title') ?>
				
				<?= $form->field($model, 'descr')->textarea(['rows' => 7, 'value' => html_entity_decode($model->descr)]) ?>
				
				<?= $form->field($model, 'telegram', [
					'template' => '{beginLabel}{labelTitle}{endLabel}<div class="input-group"><span class="input-group-addon">@</span>{input}</div>{error}{hint}'
				]); ?>
				
			</div>
		</div>
	</fieldset>
	
	<fieldset>
		<legend><?= Yii::t('tag', 'header_links') ?></legend>
		
		<?= $form->field($model, 'links_ids')->widget(Selectize::class, [
			'items' => Tag::listDataType('id', 'title', 'title', [Type::FILTER_GROUP, Type::FILTER]),
			'pluginOptions' => [
				'plugins' => ['remove_button'],
				'persist' => false,
				'createOnBlur' => false,
				'create' => false,
                'valueField' => 'id',
                'searchField' => 'title',
                'options' => Tag::listWithColorsType([Type::FILTER_GROUP, Type::FILTER]),
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

        <?php //\common\modules\base\components\Debug::dump($model->catalogFieldGroupTags);die; ?>
        <?= $form->field($model, 'catalog_field_group_ids')->widget(Selectize::class, [
            'items' => \common\modules\catalog\models\CatalogFieldGroup::listData('id', 'title', 'title'),
            'pluginOptions' => [
                'plugins' => ['remove_button'],
                'persist' => false,
                'createOnBlur' => false,
                'create' => false,
                'valueField' => 'id',
                'searchField' => 'title',
                //'options' => Tag::listWithColorsType([Type::FILTER_GROUP, Type::FILTER]),
            ],
            'options' => [
                'multiple' => true,
                'class' => 'form-control',
            ]
        ]); ?>

		<?= $form->field($model, 'multiple')->checkbox() ?>
        <?= $form->field($model, 'visible_preview')->checkbox() ?>
	
	</fieldset>
	
	<fieldset>
		<legend><?= Yii::t('tag', 'header_other') ?></legend>
		
		<?= $form->field($model, 'status')->widget(Select2::class, [
			'items' => Status::listData(),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
		
		<?= $form->field($model, 'sequence') ?>
		
	</fieldset>
	
	<fieldset>
		<legend><?= Yii::t('tag', 'header_seo') ?></legend>
		
		<?= SeoFormWidget::widget([
			'model' => $model,
			'form' => $form,
		]); ?>
	</fieldset>
	
	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
		<?php if (Yii::$app->user->can('tag.default.index')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
				'class' => 'btn btn-default btn-lg'
			]) ?>
		<?php } ?>
	</div>
	
	<?php ActiveForm::end(); ?>

</div>

