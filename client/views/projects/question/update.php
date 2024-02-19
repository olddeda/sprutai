<?php

/* @var $this yii\web\View */
/* @var $project \common\modules\project\models\Project */
/* @var $model \common\modules\content\models\Question */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['projects/default/index']];
$this->params['breadcrumbs'][] = ['label' => $project->title, 'url' => ['projects/default/view', 'id' => $project->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('project-question', 'title'), 'url' => ['index', 'project_id' => $project->id]];
$this->params['breadcrumbs'][] = Yii::t('project-question', 'title_update');

$this->title = Yii::t('project-question', 'title_update');

?>

<div class="projects-questions-update detail-view">
	
	<?= $this->render('../default/_header', [
		'model' => $project,
		'question' => $model,
	]) ?>
	
	<div class="content-index padding-20">
		
		<?= $this->render('_form', [
			'model' => $model,
			'project' => $project,
			'isCreate' => false,
		]) ?>
		
	</div>
	
</div>
