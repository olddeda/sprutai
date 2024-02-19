<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\modules\user\widgets\Connect;

/**
 * @var yii\web\View $this
 * @var common\modules\user\models\SigninForm $model
 * @var common\modules\user\Module $module
 */

$this->title = Yii::t('user', 'title_signin');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                    'validateOnBlur' => false,
                    'validateOnType' => false,
                    'validateOnChange' => false,
                ]) ?>

                <?= $form->field($model, 'login', ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'tabindex' => '1']]) ?>

                <?= $form->field($model, 'password', ['inputOptions' => ['class' => 'form-control', 'tabindex' => '2']])->passwordInput()->label(Yii::t('user', 'field_password').($module->enablePasswordRecovery ? ' (' . Html::a(Yii::t('user', 'link_forgot_password'), ['/user/forgot/index'], ['tabindex' => '5']) . ')' : '')) ?>

                <?= $form->field($model, 'rememberMe')->checkbox(['tabindex' => '4']) ?>

                <?= Html::submitButton(Yii::t('user', 'link_sign_in'), ['class' => 'btn btn-primary btn-block', 'tabindex' => '3']) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <?php if ($module->enableConfirmation) { ?>
            <p class="text-center">
                <?= Html::a(Yii::t('user', 'link_didnt_receive_confirmation_message'), ['/user/signup/resend']) ?>
            </p>
        <?php } ?>
        <?php if ($module->enableRegistration) { ?>
            <p class="text-center">
                <?= Html::a(Yii::t('user', 'link_dont_have_an_account'), ['/user/signup']) ?>
            </p>
        <?php } ?>

		<div class="margin-top-30 align-center">
			<?= Connect::widget([
				'baseAuthUrl' => ['/user/security/auth'],
			]) ?>
		</div>
    </div>
</div>
