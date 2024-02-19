<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\base\helpers\enum\ModuleType;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $company \common\modules\company\models\Company */
/* @var $model \common\modules\content\models\Question */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = $model->title ? $model->title : Yii::t('company-question', 'title_view');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['companies/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['companies/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('company-question', 'title'), 'url' => ['index', 'company_id' => $company->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="companies-questions-view detail-view">
	
	<?= $this->render('../default/_header', [
		'model' => $company,
		'question' => $model,
	]) ?>

	<div class="content-index padding-20 comments">
		<div class="row">
			<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
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
									<div>
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
								<div class="comment-body"><?= Yii::$app->formatter->asHtml($model->text_with_links) ?></div>
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
				
				<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('company-question', 'button_back'), ['index', 'company_id' => $company->id], [
					'class' => 'btn btn-default btn-lg margin-bottom-15'
				]) ?>
			</div>
			<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
				<?= $this->render('../default/_view_contacts', ['model' => $company]) ?>
				<?= $this->render('../default/_view_discount', ['model' => $company]) ?>
				<?= $this->render('../default/_view_questions', ['model' => $company]) ?>
			</div>
		</div>
	</div>

	<div class="content-index padding-20 comments">
	

	</div>

</div>
