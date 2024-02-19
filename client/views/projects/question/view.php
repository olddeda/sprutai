<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\base\helpers\enum\ModuleType;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $project \common\modules\project\models\Project */
/* @var $model \common\modules\content\models\Question */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['projects/default/index']];
$this->params['breadcrumbs'][] = ['label' => $project->title, 'url' => ['projects/default/view', 'id' => $project->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('project-question', 'title'), 'url' => ['index', 'project_id' => $project->id]];
$this->params['breadcrumbs'][] = Yii::t('project-question', 'title_view');

$this->title = Yii::t('project-question', 'title_view');

?>

<div class="projects-questions-view detail-view">
	
	<?= $this->render('../default/_header', [
		'model' => $project,
		'question' => $model,
	]) ?>
	
	<div class="content-index padding-20 comments">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="comment-content">
					<div class="comment-author-avatar">
						<div class="thumbnail">
							<?= Html::a(Html::img($model->author->avatar->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-responsive']), ['/user/profile/view', 'id' => $model->author_id], ['target' => '_blank']) ?>
						</div>
					</div>
					<div class="comment-details">
						<div class="comment-action-buttons">
							<?php if ($model->getIsOwn()) { ?>
								<?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']), ['projects/question/update', 'project_id' => $project->id, 'id' => $model->id], [
									'title' => Yii::t('base', 'button_update'),
									'data-toggle' => 'tooltip',
								]) ?>
								<?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']), ['projects/question/delete', 'project_id' => $project->id, 'id' => $model->id], [
									'title' => Yii::t('base', 'button_delete'),
									'data-toggle' => 'tooltip',
									'data-method' => 'POST',
									'data-confirm' => Yii::t('project-question', 'confirm_delete'),
								]) ?>
								<div class="clearfix"></div>
							<?php } ?>
							<div class="">
								<?= $this->render('//statistics/_comments', [
									'model' => $model,
								]) ?>
								<?= $this->render('//statistics/_visit', [
									'model' => $model,
								]) ?>
								<div class="votes inline">
									<div class="vote margin-top-0">
										<?= \common\modules\vote\widgets\Vote::widget([
											'viewFile' => '@client/views/vote/vote',
											'entity' => \common\modules\vote\models\Vote::CONTENT_VOTE,
											'model' => $model,
											'moduleType' => \common\modules\base\helpers\enum\ModuleType::CONTENT,
											'options' => ['class' => 'vote vote-visible-buttons']
										]); ?>
									</div>
								</div>
							</div>
						</div>
						<div class="comment-author-name">
							<span><?= Html::a($model->authorName, ['/user/profile/view', 'id' => $model->author_id], ['target' => '_blank']) ?></span>
							<span class="comment-date"><?= Yii::$app->formatter->asRelativeTime($model->date_at) ?></span>
						</div>
						<div class="comment-body"><?= Yii::$app->formatter->asHtml($model->text) ?></div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>

		<div class="content-comments">
			<?= \common\modules\comments\widgets\CommentWidget::widget([
				'moduleType' => ModuleType::CONTENT,
				'model' => $model,
				'commentView' => '@client/views/comments/index',
				'relatedTo' => Yii::t('comments', 'related_to_text', [
					'title' => $model->title,
					'url' => Url::current(),
				]),
			]); ?>
		</div>
		
		<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('project-question', 'button_back'), ['index', 'project_id' => $project->id], [
			'class' => 'btn btn-default btn-lg margin-bottom-15'
		]) ?>
		
	</div>

</div>
