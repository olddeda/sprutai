<?php

use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model common\modules\project\models\Project */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = $model->title.' - '.Yii::t('project', 'title_event');

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>

<div class="project-view-event detail-view">
	
	<?= $this->render('_header', [
		'model' => $model,
	]) ?>

	<div class="content-index margin-20">
		<?= ListView::widget([
			'dataProvider' => $dataProvider,
			'itemView' => '_view_event',
			'layout' => "{items}\n{pager}",
			'emptyText' => Yii::t('project', 'empty_events'),
		]); ?>
	</div>
</div>
