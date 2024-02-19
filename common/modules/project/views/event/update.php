<?php

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Event */
/* @var $project common\modules\project\models\Project */

$this->context->layoutContent = 'content_no_panel';
$this->context->layoutSidebar = null;
$this->context->bodyClass .= ' sidebar-content';

$this->title = Yii::t('project-event', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['/project/default/index']];
$this->params['breadcrumbs'][] = ['label' => $project->title, 'url' => ['/project/default/view', 'id' => $project->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('project-event', 'title'), 'url' => ['index', 'project_id' => $project->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="project-event-update">

    <?= $this->render('_form', [
        'model' => $model,
        'project' => $project,
    ]) ?>

</div>

