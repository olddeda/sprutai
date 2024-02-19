<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\modules\user\models\User $user
 * @var common\modules\user\Module $module
 */

$this->context->layout = '//main_single';
$this->context->layoutContent = '//layouts/content_clear';

$this->title = Yii::t('user', 'title_signup');

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
			
			<?= $form->field($model, 'email', [
				'inputOptions' => [
					'class' => 'form-control',
					'autofocus' => 'autofocus',
					'tabindex' => '1',
					'placeholder' => Yii::t('user', 'placeholder_email'),
				],
				'options' => ['class' => 'form-group has-feedback'],
				'template' => '{input}<span class="fa fa-envelope form-control-feedback text-muted"></span>{hint}{error}',
			]) ?>
			
			<?= $form->field($model, 'username', [
				'inputOptions' => [
					'class' => 'form-control',
					'autofocus' => 'autofocus',
					'tabindex' => '1',
					'placeholder' => Yii::t('user', 'placeholder_username'),
				],
				'options' => ['class' => 'form-group has-feedback'],
				'template' => '{input}<span class="fa fa-user form-control-feedback text-muted"></span>{hint}{error}',
			]) ?>
			
			<?php if ($module->enableGeneratingPassword == false) { ?>
			<?= $form->field($model, 'password', [
				'inputOptions' => [
					'class' => 'form-control',
					'tabindex' => '2',
					'placeholder' => Yii::t('user', 'placeholder_password'),
				],
				'options' => ['class' => 'form-group has-feedback'],
				'template' => '{input}<span class="fa fa-lock form-control-feedback text-muted"></span>{hint}{error}',
			])->passwordInput() ?>
			<?php } ?>
			
			<?= Html::submitButton(Yii::t('user', 'link_signup'), ['class' => 'btn btn-block btn-primary mt-lg', 'tabindex' => '3']) ?>

			<p class="margin-10 margin-top-20">Регистрируясь на сайте вы соглашаетесь с <a href="/client/rules" target="_blank">правилами пользования сайтом</a>, <a href="/client/policy" target="_blank">политикой конфиденциальности</a> и <a href="/client/registration-rules">условиями предоставления услуг.</a></p>
			
			<?php ActiveForm::end(); ?>

			<p class="pt-lg text-center"><?= Yii::t('user', 'link_already_registered') ?></p>
			<?= Html::a(Yii::t('user', 'link_signin'), ['/user/signin'], ['class' => 'btn btn-block btn-default']) ?>
		</div>
	</div>
	<!-- END panel-->

	<div class="p-lg text-center">
		<span>&copy;</span>
		<span>2012 - <?= date('Y') ?></span>
		<span><br/></span>
		<span><?= Yii::$app->name ?></span>
	</div>
	
</div>
