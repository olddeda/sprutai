<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/*
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var common\modules\user\models\ForgotForm $model
 */

$this->context->layout = '//main_single';
$this->context->layoutContent = '//layouts/content_clear';

$this->title = Yii::t('user', 'title_signup_confirm');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'title_signin'), 'url' => ['/user/signin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert') ?>