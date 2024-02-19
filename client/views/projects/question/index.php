<?php

use yii\helpers\Html;
use yii\widgets\ListView;

use common\modules\comments\CommentAsset;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $project \common\modules\project\models\Project */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['companies/default/index']];
$this->params['breadcrumbs'][] = ['label' => $project->title, 'url' => ['projects/default/view', 'id' => $project->id]];
$this->params['breadcrumbs'][] = Yii::t('project-question', 'title');

$this->title = Yii::t('project-question', 'title');

CommentAsset::register($this);
?>

<div class="projects-questions-index detail-view">
	
	<?= $this->render('../default/_header', ['model' => $project]) ?>

	<div class="content-index padding-20">
		<?php if ($dataProvider->totalCount) { ?>
			<?= ListView::widget([
				'dataProvider' => $dataProvider,
				'itemView' => '_view',
				'viewParams' => [
					'project' => $project,
				],
				'layout' => "{items}\n{pager}"
			]); ?>
		<?php } else { ?>
		<div class="panel panel-default">
			<div class="panel-body">
				<?= Yii::t('project-question', 'error_empty_list_user') ?>
			</div>
		</div>
		<?php } ?>

		<div class="form-group margin-top-20">
			<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('project-question', 'button_add'), ['projects/question/create', 'project_id' => $project->id], [
				'class' => 'btn btn-primary btn-lg'
			]) ?>
		</div>
	</div>

</div>
