<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->context->layout = '//main_single';
$this->context->layoutContent = '//layouts/content_clear';

$this->title = Yii::t('user', 'title_forgot_reset');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'title_signin'), 'url' => ['/user/signin']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'title_forgot'), 'url' => ['/user/forgot']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-signin block-center mt-xl wd-xxl">

	<!-- START panel-->
	<div class="panel panel-primary panel-flat">
		<div class="panel-heading text-center">
			<?= Html::a(Html::img('@web/images/svg/logo.svg', [
				'class' => 'block-center img-rounded',
			])) ?>
		</div>
		<div class="panel-body">
			<h1 class="text-center pv"><?= $this->title ?></h1>
			
			<?php $form = ActiveForm::begin([
				'id' => 'login-form',
				'enableAjaxValidation' => true,
				'enableClientValidation' => false,
				'validateOnBlur' => false,
				'validateOnType' => false,
				'validateOnChange' => false,
				'options' => [
					'class' => 'mb-lg',
					'role' => 'form',
				],
			]) ?>
			
			<?= $form->field($model, 'password', [
				'inputOptions' => [
					'class' => 'form-control',
					'autofocus' => 'autofocus',
					'tabindex' => '1',
					'placeholder' => Yii::t('user', 'placeholder_password_new'),
				],
				'options' => ['class' => 'form-group has-feedback'],
				'template' => '{input}<span class="fa fa-envelope form-control-feedback text-muted"></span>{hint}{error}',
			])->passwordInput() ?>
			
			<?= Html::submitButton(Yii::t('user', 'link_finish'), ['class' => 'btn btn-block btn-primary mt-lg', 'tabindex' => '3']) ?>
			
			<?php ActiveForm::end(); ?>

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