<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $commentModel \common\modules\comments\models\Comment */
/* @var $encryptedEntity string */
/* @var $formId string comment form id */
?>
<div class="comment-form-container">
    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => $formId,
            'class' => 'comment-box'
        ],
        'action' => Url::to(['/comments/default/create', 'entity' => $encryptedEntity]),
        'validateOnChange' => false,
        'validateOnBlur' => false
    ]); ?>

    <?php echo $form->field($commentModel, 'content', ['template' => '{input}{error}'])->textarea([
		'placeholder' => Yii::t('comments', 'field_content_placeholder'),
		'rows' => 4,
		'data' => ['comment' => 'content']
	]) ?>
	
    <?php echo $form->field($commentModel, 'parent_id', ['template' => '{input}'])->hiddenInput([
		'data' => ['comment' => 'parent-id']
	]); ?>

	<div class="comment-box-partial">
        <div class="button-container show">
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
    </div>

    <?php $form->end(); ?>

    <div class="clearfix"></div>
</div>
