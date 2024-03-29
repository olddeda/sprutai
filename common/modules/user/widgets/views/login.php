<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/*
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var common\modules\user\models\forms\SigninForm $model
 * @var string $action
 */
?>

<?php if (Yii::$app->user->isGuest) { ?>

    <?php $form = ActiveForm::begin([
        'id' => 'login-widget-form',
        'fieldConfig' => [
            'template' => "{input}\n{error}",
        ],
        'action' => $action,
    ]) ?>

    <?= $form->field($model, 'login')->textInput(['placeholder' => 'Login']) ?>
    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password']) ?>
    <?= $form->field($model, 'rememberMe')->checkbox() ?>

    <?= Html::submitButton(Yii::t('user', 'Sign in'), ['class' => 'btn btn-primary btn-block']) ?>

    <?php ActiveForm::end(); ?>

<?php } else { ?>
    <?= Html::a(Yii::t('user', 'Logout'), ['/user/security/logout'], ['class' => 'btn btn-danger btn-block', 'data-method' => 'post']) ?>
<?php } ?>
