<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\payment\models\PaymentType */

$this->title = Yii::t('payment-type', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('payment-type', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="payment-type-update">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
