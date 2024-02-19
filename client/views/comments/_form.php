<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\components\Debug;

use common\modules\company\models\Company;

/* @var $this \yii\web\View */
/* @var $commentModel \common\modules\comments\models\Comment */
/* @var $encryptedEntity string */
/* @var $formId string comment form id */
?>
<div class="comment-form-container">
	
	<?php $form = ActiveForm::begin([
		'options' => [
			'id' => $formId,
			'class' => 'comment-box',
			'data-sending' => Yii::t('comments', 'state_sending'),
		],
		'action' => Url::to(['/comments/default/create', 'entity' => $encryptedEntity]),
		'validateOnChange' => false,
		'validateOnBlur' => false,
	]); ?>

	<div class="panel panel-default">
		<div class="panel-body">
			
			<?= $form->field($commentModel, 'content')->widget(common\modules\base\extensions\imperavi\Widget::class, [
				'settings' => [
					'lang' => 'ru',
					'toolbar' => true,
					'focus' => false,
					'buttons' => ['bold', 'italic', 'ul', 'ol', 'link', 'image'],
					'minHeight' => 300,
					'imageUpload' => Url::to([
						'/media/imperavi/upload',
						'module_type' => ModuleType::COMMENT,
						'module_id' => $commentModel->id,
					]),
					'imageManagerJson' => Url::to([
						'/media/imperavi/index',
						'module_type' => ModuleType::COMMENT,
					]),
					'plugins' => [
						'imagemanager',
					],
				],
			])->label(false) ?>
			
			<?php echo $form->field($commentModel, 'parent_id', ['template' => '{input}'])->hiddenInput([
				'data' => ['comment' => 'parent-id']
			]); ?>
			
			<?php echo Html::hiddenInput('comment-id', '', [
				'data' => ['comment' => 'id']
			]); ?>

			<div class="comment-box-partial">
				<div class="button-container show inline align-top margin-bottom-5">
					<?php echo Html::submitButton(Yii::t('comments', 'button_comment_send'), [
						'class' => 'btn btn-primary',
						'data-title-send' => Yii::t('comments', 'button_comment_send'),
						'data-title-save' => Yii::t('comments', 'button_comment_save'),
					]); ?>
					<?php echo Html::a(Yii::t('comments', 'button_comment_reply_cancel'), '#', [
						'id' => 'cancel-reply',
						'class' => 'btn btn-default',
						'data' => ['action' => 'cancel-reply']
					]); ?>
				</div>
				<?php if ($companies = Company::findByUserId(Yii::$app->user->id)) { ?>
				<div class="inline align-top">
					<?= $form->field($commentModel, 'company_id', [
						'template' => '{input}',
						'options' => ['class' =>'form-group margin-0']
					])->widget(Select2::class, [
						'items' => ArrayHelper::merge([
							0 => Yii::$app->user->identity->getFio(),
						], ArrayHelper::map($companies, 'id', function($data) {
							return Yii::t('content', 'author_type_company').': '.$data->title;
						})),
						'clientOptions' => [
							'hideSearch' => true,
						],
					]) ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
			
	<?php $form->end(); ?>

	<div class="clearfix"></div>
	
</div>
