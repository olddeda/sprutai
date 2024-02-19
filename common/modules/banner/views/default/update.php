<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\banner\models\Banner */

$this->title = Yii::t('banner', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('banner', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="banner-update">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
