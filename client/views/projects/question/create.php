<?php

/* @var $this yii\web\View */
/* @var $project \common\modules\project\models\Project */
/* @var $model \common\modules\content\models\Question */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['projects/default/index']];
$this->params['breadcrumbs'][] = ['label' => $project->title, 'url' => ['projects/default/view', 'id' => $project->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('project-question', 'title'), 'url' => ['index', 'project_id' => $project->id]];
$this->params['breadcrumbs'][] = Yii::t('project-question', 'title_create');

$this->title = Yii::t('project-question', 'title_create');

?>

<div class="projects-questions-create detail-view">
	
	<?= $this->render('../default/_header', ['model' => $project]) ?>
	
	<div class="content-index padding-20">
		
		<?= $this->render('_form', [
			'model' => $model,
			'project' => $project,
			'isCreate' => true,
		]) ?>
		
	</div>
	
</div>
