<?php

use yii\helpers\Html;

/*
 * @var yii\web\View $this
 */

$this->title = Yii::t('user', 'title_signup_confirm_request');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'title_signin'), 'url' => ['/user/signin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert') ?>