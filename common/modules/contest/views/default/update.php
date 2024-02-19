<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\contest\models\Contest */

$this->title = Yii::t('contest', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('contest', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="contest-update">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
