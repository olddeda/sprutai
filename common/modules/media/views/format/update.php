<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\media\models\MediaFormat */

$this->title = Yii::t('media-format', 'update_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('media', 'title'), 'url' => ['/media/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('media-format', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->format, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('media-format', 'update_title');

?>
<div class="media-format-update">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
