<?php

use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Question */
/* @var $company \common\modules\company\models\Company */

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
						<div class="margin-top-0">
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
										'options' => ['class' => 'vote vote-visible-buttons margin-top-0']
									]); ?>
								</div>
							</div>
						</div>
                        <?php if ($model->getIsOwn()) { ?>
						<div class="clearfix"></div>
	                    <div class="margin-top-10">
                            <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']), ['companies/question/update', 'company_id' => $company->id, 'id' => $model->id], [
                                'title' => Yii::t('base', 'button_update'),
                                'data-toggle' => 'tooltip',
                            ]) ?>
                            <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']), ['companies/question/delete', 'company_id' => $company->id, 'id' => $model->id], [
                                'title' => Yii::t('base', 'button_delete'),
                                'data-toggle' => 'tooltip',
                                'data-method' => 'POST',
                                'data-confirm' => Yii::t('company-question', 'confirm_delete'),
                            ]) ?>
						</div>
                        <?php } ?>
					</div>
					<div class="comment-author-name">
						<span><?= Html::a($model->authorName, ['/user/profile/view', 'id' => $model->author_id], ['target' => '_blank']) ?></span>
						<span class="comment-date"><?= Yii::$app->formatter->asRelativeTime($model->date_at) ?></span>
					</div>
					<div class="comment-body">
						<?php if ($model->title) { ?>
							<h3 class="margin-top-5"><?= Html::a($model->title, ['/companies/question/view', 'company_id' => $company->id, 'id' => $model->id]) ?></h3>
                        <?php } else { ?>
							<?= Yii::$app->formatter->asHtml($model->text) ?>
						<?php } ?>

					</div>
				</div>
				<?php if (!$model->title) { ?>
				<div class="clearfix"></div>
				<hr>
				<div class="link">
					<?= Html::a(Yii::t('company-question', 'link_view'), ['/companies/question/view', 'company_id' => $company->id, 'id' => $model->id]) ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>