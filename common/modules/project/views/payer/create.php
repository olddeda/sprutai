<?php

/* @var $this yii\web\View */
/* @var $model common\modules\payment\models\Payment */
/* @var $project common\modules\project\models\Project */

$this->title = Yii::t('project-payer', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['/project/default/index']];
$this->params['breadcrumbs'][] = ['label' => $project->title, 'url' => ['/project/default/view', 'id' => $project->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('project-payer', 'title'), 'url' => ['index', 'project_id' => $project->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="project-payer-create">

    <?= $this->render('_form', [
        'model' => $model,
        'project' => $project,
    ]) ?>

</div>

