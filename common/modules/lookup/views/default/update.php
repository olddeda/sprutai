<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\lookup\models\Lookup */

$this->title = Yii::t('lookup', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('lookup', 'title_index'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
