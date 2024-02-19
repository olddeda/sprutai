<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Page */

$this->title = Yii::t('seo-module', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('seo-module', 'title'), 'url' => ['/seo/module/index']];
$this->params['breadcrumbs'][] = Yii::t('seo-module', 'title_update');
?>

<div class="seo-module-update">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
