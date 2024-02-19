<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\shortener\models\Shortener */

$this->title = Yii::t('shortener', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('shortener', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="shortener-update">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
