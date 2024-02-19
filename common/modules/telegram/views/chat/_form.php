<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\selectize\Selectize;
use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\Boolean;

use common\modules\tag\models\Tag;
use common\modules\tag\helpers\enum\Type as TagType;

/* @var $this yii\web\View */
/* @var $model common\modules\telegram\models\TelegramChat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="telegram-chat-form">

    <?php $form = ActiveForm::begin([
		'id' => 'version-form',
		'enableAjaxValidation' => false,
	]); ?>

	<fieldset>
		<legend><?= Yii::t('telegram-chat', 'header_general') ?></legend>

		<div class="grid">
			<div class="col width-200">
				<?= $model->logo->uploaderImageSlim([
					'settings' => [
						'size' => [
							'width' => 600,
							'height' => 600,
						],
					],
				]); ?>
			</div>
			<div class="col-auto">
				
				<?= $form->field($model, 'title')->textInput() ?>
				<?= $form->field($model, 'identifier')->textInput() ?>
				
				<?= $form->field($model, 'username', [
					'template' => '{beginLabel}{labelTitle}{endLabel}<div class="input-group"><span class="input-group-addon">@</span>{input}</div>{error}{hint}'
				]); ?>

				<?= $form->field($model, 'tags_ids')->widget(Selectize::class, [
					'items' => Tag::listDataType('id', 'title', 'title', [TagType::SYSTEM]),
					'pluginOptions' => [
						'plugins' => ['remove_button'],
						'persist' => false,
						'createOnBlur' => false,
						'create' => false,
						'valueField' => 'id',
						'searchField' => 'title',
						'options' => Tag::listWithColorsType([TagType::SYSTEM]),
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
				
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
                <?= $form->field($model, 'description')->textarea(['rows' => 8]) ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?= Yii::t('telegram-chat', 'header_other') ?></legend>

        <?= $form->field($model, 'is_partner')->widget(Select2::class, [
            'items' => Boolean::listData(),
            'clientOptions' => [
                'hideSearch' => true,
            ]
        ]) ?>

        <?= $form->field($model, 'is_channel')->widget(Select2::class, [
            'items' => Boolean::listData(),
            'clientOptions' => [
                'hideSearch' => true,
            ]
        ]) ?>

        <?= $form->field($model, 'is_spam_protect')->widget(Select2::class, [
            'items' => Boolean::listData(),
            'clientOptions' => [
                'hideSearch' => true,
            ]
        ]) ?>

		<?= $form->field($model, 'notify_content')->widget(Select2::class, [
			'items' => Boolean::listData(),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
		
		<?= $form->field($model, 'notify_payment')->widget(Select2::class, [
			'items' => Boolean::listData(),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
		
		<?= $form->field($model, 'status')->widget(Select2::class, [
			'items' => Status::listData(),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
	</fieldset>

	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
		<?php if (Yii::$app->user->can('telegram.chat.index')) { ?>
		<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
			'class' => 'btn btn-default btn-lg'
		]) ?>
		<?php } ?>
	</div>

    <?php ActiveForm::end(); ?>

</div>
