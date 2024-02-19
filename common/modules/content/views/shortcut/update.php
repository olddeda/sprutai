<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Shortcut */

$this->title = Yii::t('content-shortcut', 'title_update');

//$this->params['breadcrumbs'][] = ['label' => Yii::t('content', 'title'), 'url' => ['/content/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('content-shortcut', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->context->layoutContent = 'content_no_panel';
$this->context->layoutSidebar = null;
$this->context->bodyClass .= ' sidebar-content';

?>

<div class="content-shortcut-update">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
