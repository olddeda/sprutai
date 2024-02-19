<?php

use yii\helpers\Url;

use common\modules\base\helpers\enum\ModuleType;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model common\modules\project\models\Project */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('project', 'title_comment');

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>

<div class="project-view-event detail-view">
	
	<?= $this->render('_header', [
		'model' => $model,
	]) ?>

	<div class="content-comments margin-20">
		<?= common\modules\comments\widgets\CommentWidget::widget([
			'moduleType' => ModuleType::CONTENT_PROJECT,
			'model' => $model,
			'commentView' => '@client/views/comments/index',
			'relatedTo' => Yii::t('comments', 'related_to_text', [
				'title' => $model->title,
				'url' => Url::current(),
			]),
		]); ?>
	</div>
	
</div>
