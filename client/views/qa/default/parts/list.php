<?php
/**
 * @var \artkost\qa\models\Question[] $models
 */
use artkost\qa\Module;
use yii\helpers\Html;

?>

<div class="qa-list list-group">
	<?php if (!empty($models)): foreach ($models as $model): ?>
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="list-group-item clearfix qa-item" id="question-<?= $model->id ?>">
					<div class="qa-panels">
						<div class="qa-panel votes">
							<div class="mini-counts"><?= $model->votes ?></div>
							<div><span class="glyphicon glyphicon-ok" title="<?= Yii::t('qa', 'panel_votes') ?>"></span></div>
						</div>
						<div class="qa-panel <?= ($model->answers > 0) ? 'status-answered' : 'status-unanswered' ?>">
							<div class="mini-counts"><?= $model->getAnswers()->andWhere(['status' => 1])->count() ?></div>
							<div><span class="glyphicon glyphicon-comment" title="<?= Yii::t('qa', 'panel_answers') ?>"></span></div>
						</div>
						<div class="qa-panel views">
							<div class="mini-counts"><?= $model->views ?></div>
							<div><span class="glyphicon glyphicon-eye-open" title="<?= Yii::t('qa', 'panel_views') ?>"></span></div>
						</div>
					</div>
					<div class="qa-summary">
						<div class="question-meta">
							<div><?= $this->render('created', ['model' => $model]) ?></div>
							<div><?= $this->render('edit-links', ['model' => $model]) ?></div>
						</div>
						<h4 class="question-heading list-group-item-heading">
							<a href="<?= Module::url(['view', 'id' => $model->id, 'alias' => $model->alias]) ?>" class="question-link" title="<?= Html::encode($model->title) ?>"><?= Html::encode($model->title) ?></a>
							<?php if ($model->isDraft()): ?>
								<small><span class="label label-default"><?= Yii::t('qa', 'Draft') ?></span></small>
							<?php endif; ?>
						</h4>
						<div class="question-tags">
							<?= $this->render('tags-list', ['model' => $model]) ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	<?php endforeach; else: ?>
		<div class="list-group-item qa-item-not-found">
			<h4 class="list-group-item-heading question-heading"><?= Yii::t('qa', 'error_empty_list') ?></h4>
		</div>
	<?php endif; ?>
</div>