<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Page */

$this->title = Yii::t('content-page', 'title_create');

//$this->params['breadcrumbs'][] = ['label' => Yii::t('content', 'title'), 'url' => ['/content/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('content-page', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->context->layoutContent = 'content_no_panel';
$this->context->layoutSidebar = null;
$this->context->bodyClass .= ' sidebar-content';
?>

<div class="content-page-create">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
