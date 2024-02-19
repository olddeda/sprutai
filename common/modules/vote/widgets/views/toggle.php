<?php

use yii\helpers\Html;

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
<div class="<?= $options['class'] ?>" data-rel="<?= $jsCodeKey ?>" data-entity="<?= $entity ?>" data-entity-id="<?= $entityId ?>" data-module-type="<?= $moduleType ?>" data-user-value="<?= $userValue ?>">
    <button class="vote-btn <?= $buttonOptions['class'] ?> <?= $userValue === Vote::VOTE_POSITIVE ? 'vote-active' : '' ?>" data-action="toggle">
        <span class="vote-icon"><?= $buttonOptions['icon'] ?></span>
        <span class="vote-label"><?= Html::encode($buttonOptions['label']) ?></span>
        <span class="vote-count"><?= $count ?></span>
    </button>
</div>
