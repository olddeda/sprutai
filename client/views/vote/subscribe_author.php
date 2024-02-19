<?php

use yii\helpers\Html;
use yii\helpers\Url;

use common\modules\vote\models\Vote;

/* @var $jsCodeKey string */
/* @var $moduleType integer */
/* @var $entity string */
/* @var $entityId integer */
/* @var $model \yii\db\ActiveRecord */
/* @var $userValue null|integer */
/* @var $count integer */
/* @var $options array */
/* @var $buttonOptions array */

?>
<div class="<?= $options['class'] ?>" data-rel="<?= $jsCodeKey ?>" data-entity="<?= $entity ?>" data-entity-id="<?= $entityId ?>" data-module-type="<?= $moduleType ?>"  data-user-value="<?= $userValue ?>">
	<?php if (!Yii::$app->user->isGuest) { ?>
    <button class="vote-btn btn bnt-lg btn-default <?= $buttonOptions['class'] ?> <?= $userValue === Vote::VOTE_POSITIVE ? 'vote-active' : '' ?>"
            data-label-add="<?= Html::encode($buttonOptions['labelAdd']) ?>"
            data-label-remove="<?= Html::encode($buttonOptions['labelRemove']) ?>"
            data-action="toggle">
        <span class="vote-label"><?= Html::encode($buttonOptions[$userValue == Vote::VOTE_POSITIVE ? 'labelRemove' : 'labelAdd']) ?></span>
	    <span class="vote-count"><?= $count ?></span>
    </button>
	<?php } else { ?>
	<a class="vote-btn btn bnt-lg btn-danger <?= $buttonOptions['class'] ?>" href="<?= Url::to(['/user/signin']) ?>">
		<span class="vote-label"><?= Html::encode($buttonOptions[$userValue == Vote::VOTE_POSITIVE ? 'labelRemove' : 'labelAdd']) ?></span>
		<span class="vote-count"><?= $count ?></span>
	</a>
	<?php } ?>
</div>
