<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\modules\user\widgets\Connect;

/**
 * @var yii\web\View $this
 * @var common\modules\user\models\SigninForm $model
 * @var common\modules\user\Module $module
 */

$this->context->layout = '//main_single';
$this->context->layoutContent = '//layouts/content_clear';

$this->title = Yii::t('user', 'title_signin');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-signin block-center mt-xl wd-xxl">
	
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
			
			<?= $form->field($model, 'login', [
				'inputOptions' => [
					'class' => 'form-control',
					'autofocus' => 'autofocus',
					'tabindex' => '1',
					'placeholder' => Yii::t('user', 'placeholder_login'),
				],
				'options' => ['class' => 'form-group has-feedback'],
				'template' => '{input}<span class="fa fa-envelope form-control-feedback text-muted"></span>{hint}{error}',
			]) ?>
			
			<?= $form->field($model, 'password', [
				'inputOptions' => [
					'class' => 'form-control',
					'tabindex' => '2',
					'placeholder' => Yii::t('user', 'placeholder_password'),
				],
				'options' => ['class' => 'form-group has-feedback'],
				'template' => '{input}<span class="fa fa-lock form-control-feedback text-muted"></span>{hint}{error}',
			])->passwordInput() ?>
			
			<div class="clearfix">
				<div class="checkbox c-checkbox pull-left mt0">
					<label>
						<input type="checkbox" name="login-form[anotherComputer]" value="1" tabindex="4" />
						<span class="fa fa-check"></span><?= $model->getAttributeLabel('anotherComputer') ?>
					</label>
				</div>
				<div class="pull-right">
					<?= Html::a(Yii::t('user', 'link_forgot_password'), ['/user/forgot/index'], ['tabindex' => '5', 'class' => 'text-muted']) ?>
				</div>
			</div>
			
			<?= Html::submitButton(Yii::t('user', 'link_signin'), ['class' => 'btn btn-block btn-primary mt-lg', 'tabindex' => '3']) ?>
			
			<?php ActiveForm::end(); ?>
			
			<p class="pt-lg text-center"><?= Yii::t('user', 'message_need_account') ?></p>
			<?= Html::a(Yii::t('user', 'link_signup'), ['/user/signup'], ['class' => 'btn btn-block btn-default']) ?>
			
			<div class="margin-top-30 align-center">
				<?= Connect::widget([
					'baseAuthUrl' => ['/user/security/auth'],
				]) ?>
			</div>
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
