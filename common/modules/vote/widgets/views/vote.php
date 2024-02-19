<?php

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

?>
<div class="<?= $options['class'] ?>" data-rel="<?= $jsCodeKey ?>" data-entity="<?= $entity ?>" data-entity-id="<?= $entityId ?>" data-module-type="<?= $moduleType ?>" data-user-value="<?= $userValue ?>">
	<button class="vote-btn vote-up <?= $userValue === Vote::VOTE_POSITIVE ? 'vote-active' : '' ?>" data-action="positive">
		<i class="glyphicon glyphicon-arrow-up"></i>
	</button>
	<div class="vote-count">
        <span><?= $positive - $negative ?></span>
    </div>
	<button class="vote-btn vote-down <?= $userValue === Vote::VOTE_NEGATIVE ? 'vote-active' : '' ?>" data-action="negative">
		<i class="glyphicon glyphicon-arrow-down"></i>
	</button>
</div>
