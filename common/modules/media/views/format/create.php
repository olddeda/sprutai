<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\media\models\MediaFormat */

$this->title = Yii::t('media-format', 'create_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('media', 'title'), 'url' => ['/media/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('media-format', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="media-format-create">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
