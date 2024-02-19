<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\paste\models\Paste */

$this->title = Yii::t('paste', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('paste', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="paste-update">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
