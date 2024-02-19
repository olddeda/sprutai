<?php

/* @var $this yii\web\View */
/* @var $model common\modules\project\models\Project */

$this->title = Yii::t('project', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->context->layoutContent = 'content_no_panel';
$this->context->layoutSidebar = null;
$this->context->bodyClass .= ' sidebar-content';

?>

<div class="project-default-create">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
