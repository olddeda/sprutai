<?php

/* @var $this yii\web\View */
/* @var $model common\modules\plugin\models\Project */

$this->title = Yii::t('plugin', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->context->layoutContent = 'content_no_panel';
$this->context->layoutSidebar = null;
$this->context->bodyClass .= ' sidebar-content';

?>

<div class="plugin-default-create">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
