<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/*
 * @var yii\web\View $this
 * @var common\modules\user\models\ResendForm $model
 */

$this->context->layout = '//main_single';
$this->context->layoutContent = '//layouts/content_clear';

$this->title = Yii::t('user', 'title_signup_confirm_request');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'title_signin'), 'url' => ['/user/signin']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-signup block-center mt-xl wd-xxl">

	<!-- START panel-->
	<div class="panel panel-primary panel-flat">
		<div class="panel-heading text-center">
			<?= Html::a(Html::img('@web/images/svg/logo.svg', [
				'class' => 'block-center',
			])) ?>
		</div>
		<div class="panel-body">
			<h1 class="text-center pv"><?= $this->title ?></h1>
			<?php $form = ActiveForm::begin([
				'id' => 'resend-form',
				'enableAjaxValidation' => false,
				'enableClientValidation' => true,
			]); ?>

			<?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

			<?= Html::submitButton(Yii::t('user', 'link_continue'), ['class' => 'btn btn-primary btn-block']) ?><br>

			<?php ActiveForm::end(); ?>

			<p class="pt-lg text-center"><?= Yii::t('user', 'link_already_registered') ?></p>
			<?= Html::a(Yii::t('user', 'link_signin'), ['/user/signin'], ['class' => 'btn btn-block btn-default']) ?>
			
		</div>
	</div>
	<!-- END panel-->

	<div class="p-lg text-center">
		<span>&copy;</span>
		<span><?= date('Y') ?></span>
		<span><br/></span>
		<span><?= Yii::$app->name ?></span>
	</div>

</div>