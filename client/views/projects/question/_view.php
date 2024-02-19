<?php

use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Question */
/* @var $project \common\modules\project\models\Project */

?>

<div class="comments">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="comment-content">
				<div class="comment-author-avatar width-70">
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
				<hr>
				<div class="link">
					<?= Html::a(Yii::t('project-question', 'link_view'), ['/projects/question/view', 'project_id' => $project->id, 'id' => $model->id]) ?>
				</div>
			</div>
		</div>
	</div>
</div>