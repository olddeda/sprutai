<?php

use common\modules\vote\assets\VoteAsset;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Article */
/* @var $canVote boolean */
/* @var $showCounters boolean */

VoteAsset::register($this);

?>

<div class="votes inline">
	<div class="vote">
		<?php if ($canVote) { ?>
		<?= \common\modules\vote\widgets\Vote::widget([
			'viewFile' => '@client/views/vote/vote',
			'entity' => \common\modules\vote\models\Vote::CONTEST_VOTE,
			'model' => $model,
			'moduleType' => \common\modules\base\helpers\enum\ModuleType::CONTENT,
			'options' => ['class' => 'vote vote-visible-buttons'],
			'hideCounters' => !$showCounters,
		]); ?>
		<?php } else { ?>
		<div class="vote vote-visible-buttons">
			<button class="vote-btn vote-up" data-toggle="tooltip" data-original-title="Нравится">
				<i class="fa fa-thumbs-up"></i> <span class="vote-count-up"><?= ($showCounters ? $model->aggregateModel->positive : '') ?></span>
			</button>
			<button class="vote-btn vote-down" data-toggle="tooltip" data-original-title="Не нравится">
				<i class="fa fa-thumbs-down"></i> <span class="vote-count-down"><?= ($showCounters ? $model->aggregateModel->negative : '') ?></span>
			</button>
		</div>
		<?php } ?>
	</div>
</div>
