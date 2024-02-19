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

<?= $this->render('/_alert') ?>