<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var common\modules\user\models\User $model
 * @var common\modules\user\models\Account $account
 */

$this->context->layout = '//main_single';
$this->context->layoutContent = '//layouts/content_clear';

$this->title = Yii::t('user', 'title_signin_social');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'title_signin'), 'url' => ['/user/signin']];
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
			<h1 class="text-center pv"><?= Yii::t('user', 'message_in_order_to_finish_your_registration_we_need_you_to_enter_following_fields') ?></h1>
			
			<?php $form = ActiveForm::begin([
				'id' => 'connect-account-form',
			]); ?>
			
			<?= $form->field($model, 'email') ?>
			<?= $form->field($model, 'username') ?>
			
			<?= Html::submitButton(Yii::t('user', 'link_continue'), ['class' => 'btn btn-block btn-primary mt-lg']) ?>
			
			<?php ActiveForm::end(); ?>

			<p class="pt-lg text-center"><?= Html::a(Yii::t('user', 'message_if_you_already_registered_sign_in_and_connect_this_account_on_settings_page'), ['/user/settings/networks']) ?></p>

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
