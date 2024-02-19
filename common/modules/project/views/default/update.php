<?php

/* @var $this yii\web\View */
/* @var $model common\modules\project\models\Project */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('project', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->context->layoutContent = 'content_no_panel';
$this->context->layoutSidebar = null;
$this->context->bodyClass .= ' sidebar-content';

?>

<div class="project-default-update">
	
	<?= $this->render('_header', ['model' => $model]) ?>
	
	<div class="margin-top-20">
		<?= $this->render('_form', ['model' => $model]) ?>
	</div>
	
</div>
