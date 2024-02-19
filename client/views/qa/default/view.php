<?php
/**
 * @var \artkost\qa\models\Question $model
 * @var \yii\data\ActiveDataProvider $answerDataProvider
 * @var string $answerOrder
 * @var \artkost\qa\models\Answer $answer
 * @var \yii\web\View $this
 */

use artkost\qa\Module;
use yii\helpers\Html;

$this->context->layoutContent = 'content_no_panel';

$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => Yii::t('qa', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$answerOrders = [
    Yii::t('qa', 'answer_active') => 'active',
	Yii::t('qa', 'answer_oldest') => 'oldest',
	Yii::t('qa', 'answer_votes') => 'votes'
];

?>


<section class="qa-view">
	<article class="qa-view-question">
		<div class="panel panel-default">
			<header class="panel-heading">
				<?= $this->render('parts/created', ['model' => $model]) ?>
				<?= $this->render('parts/vote', ['model' => $model, 'route' => 'question-vote']) ?>
				<?= $this->render('parts/favorite', ['model' => $model]) ?>
			</header>
			<section class="panel-body qa-view-text">
				<?= $model->body ?>
			</section>
			<footer class="panel-footer">
				<div class="qa-view-meta">
					<?= $this->render('parts/edit-links', ['model' => $model]) ?>
					<?= $this->render('parts/tags-list', ['model' => $model]) ?>
				</div>
			</footer>
		</div>
	</article>

	<div class="qa-view-answers">
		<?php if ($answerDataProvider->totalCount): ?>
		<div class="qa-view-answers-heading">
			<ul class="qa-view-tabs nav nav-pills">
				<?php foreach ($answerOrders as $aId => $aOrder): ?>
					<li <?= ($aOrder == $answerOrder) ? 'class="active"' : '' ?> >
						<a href="<?= Module::url(['view', 'id' => $model->id, 'alias' => $model->alias, 'answers' => $aOrder]) ?>">
							<?= Module::t('main', $aId) ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

		<div class="qa-view-answers-list">
			<?php foreach ($answerDataProvider->models as $row /** @var \artkost\qa\models\Answer $row */): ?>
			<article class="qa-view-answer">
				<section class="panel panel-default">
					<header class="panel-heading">
						<?= $this->render('parts/created', ['model' => $row]) ?>
						<?= $this->render('parts/like', ['model' => $row, 'route' => 'answer-vote']) ?>
					</header>
					<section class="panel-body">
						<div class="qa-view-text">
							<?= $row->body ?>
						</div>
					</section>
					<footer class="panel-footer">
						<?= $this->render('parts/edit-links', ['model' => $row]) ?>
					</footer>
				</section>
			</article>
			<?php endforeach; ?>
		</div>

		<div class="qa-view-answer-pager">
			<?= $this->render('parts/pager', ['dataProvider' => $answerDataProvider]) ?>
		</div>

		<div class="qa-view-answer-form">
			<div class="panel panel-default">
				<div class="panel-body">
					<?= $this->render('parts/form-answer', ['model' => $answer, 'action' => Module::url(['answer', 'id' => $answer->id])]); ?>
				</div>
			</div>
		</div>
	</div>
</section>
