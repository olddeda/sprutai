<?php

use yii\helpers\Url;

use common\modules\vote\models\Vote;

/* @var $jsCodeKey string */
/* @var $moduleType integer */
/* @var $entity string */
/* @var $entityId integer */
/* @var $model \yii\db\ActiveRecord */
/* @var $userValue null|integer */
/* @var $positive integer */
/* @var $negative integer */
/* @var $rating float */
/* @var $options array */
/* @var $hideCounters bool */

?>
<div class="<?= $options['class'] ?>" data-rel="<?= $jsCodeKey ?>" data-entity="<?= $entity ?>" data-entity-id="<?= $entityId ?>" data-module-type="<?= $moduleType ?>" data-user-value="<?= $userValue ?>">
	<?php if (!Yii::$app->user->isGuest) { ?>
	<button class="vote-btn vote-up <?= $userValue === Vote::VOTE_POSITIVE ? 'vote-active' : '' ?>" data-toggle="tooltip" data-original-title="<?= Yii::t('vote', 'button_like') ?>" data-action="positive">
		<i class="fa fa-thumbs-up"></i> <span class="vote-count-up"><?= (!$hideCounters ? $positive : '') ?></span>
	</button>
	<button class="vote-btn vote-down <?= $userValue === Vote::VOTE_NEGATIVE ? 'vote-active' : '' ?>" data-toggle="tooltip" data-original-title="<?= Yii::t('vote', 'button_dislike') ?>" data-action="negative">
		<i class="fa fa-thumbs-down"></i> <span class="vote-count-down"><?= (!$hideCounters ? $negative  : '') ?></span>
	</button>
	<?php } else { ?>
	<a class="vote-btn vote-up" href="<?= Url::to(['/user/signin']) ?>" data-toggle="tooltip" data-original-title="<?= Yii::t('vote', 'button_like') ?>">
		<i class="fa fa-thumbs-up"></i> <span class="vote-count-up"><?= (!$hideCounters ? $positive : '') ?></span>
	</a>
	<a class="vote-btn vote-down href="<?= Url::to(['/user/signin']) ?>" data-toggle="tooltip" data-original-title="<?= Yii::t('vote', 'button_dislike') ?>">
		<i class="fa fa-thumbs-down"></i> <span class="vote-count-down"><?= (!$hideCounters ? $negative  : '') ?></span>
	</a>
	<?php } ?>
</div>
