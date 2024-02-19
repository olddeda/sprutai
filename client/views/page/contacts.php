<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\modules\base\components\Helper;

use common\modules\comments\widgets\CommentWidget;

use client\forms\CooperationForm;

use common\modules\base\extensions\contentbuilder\ContentBuilderAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderContentAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderSimpleLightBoxAsset;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Page */
/* @var $modelForm client\forms\ContactsForm */

$this->context->layoutContent = 'content_no_panel';

$this->title = \yii\helpers\HtmlPurifier::process($model->title);

$this->params['breadcrumbs'][] = \yii\helpers\HtmlPurifier::process($model->title);


ContentBuilderAsset::register($this);
ContentBuilderContentAsset::register($this);
ContentBuilderSimpleLightBoxAsset::register($this);

$js = <<<JS
    contentbuilderLocalize();

    $('a.is-lightbox').simpleLightbox();

    $('code.code').each(function () {
         codeMirrorHighlight($(this));
    });
JS;
$this->registerJs($js);

?>

<div class="content-view">
	
	<div class="panel panel-default">
		<div class="panel-body">
			
			<div class="is-container is-container-left container container-fluid contentbuilder-content contentbuilder">
				<?= $model->text ?>
			</div>
		</div>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-body">
			
			<?php $form = ActiveForm::begin(['id' => 'contact-form', 'enableAjaxValidation' => false]); ?>
			<div class="row">
				<div class="col-md-4">
					<?= $form->field($modelForm, 'name') ?>
				</div>
				<div class="col-md-4">
					<?= $form->field($modelForm, 'email') ?>
				</div>
				<div class="col-md-4">
					<?= $form->field($modelForm, 'phone') ?>
				</div>
			</div>
			
			<?= $form->field($modelForm, 'body')->textarea(['rows' => 6]) ?>
			
			<?php if (Yii::$app->user->isGuest) { ?>
			<?= $form->field($modelForm, 'captcha')->widget(\common\modules\base\extensions\recaptcha\ReCaptcha::class)->label(false) ?>
			<?php } ?>
			
			<div class="form-group">
				<?= Html::submitButton(Yii::t('page_cooperation', 'button_submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
			</div>
			
			<?php ActiveForm::end(); ?>
			
		</div>
	</div>

</div>
