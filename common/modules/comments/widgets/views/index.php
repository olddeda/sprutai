<?php

use yii\widgets\Pjax;

/* @var $this \yii\web\View */
/* @var $comments array */
/* @var $commentModel \common\modules\comments\models\Comment */
/* @var $maxLevel null|integer comments max level */
/* @var $encryptedEntity string */
/* @var $pjaxContainerId string */
/* @var $formId string comment form id */
?>

<?php Pjax::begin([
    'enablePushState' => false,
    'timeout' => 10000,
    'id' => $pjaxContainerId
]); ?>

<div class="row comments">
    <div class="col-md-12 col-sm-12">

		<fieldset>
			<legend><?= Yii::t('comments', 'title') ?></legend>
		</fieldset>

        <ol class="comments-list">
            <?php echo $this->render('_list', [
				'comments' => $comments,
				'maxLevel' => $maxLevel
			]) ?>
        </ol>

        <?php if (!Yii::$app->user->isGuest) { ?>
            <?php echo $this->render('_form', [
                'commentModel' => $commentModel,
                'encryptedEntity' => $encryptedEntity,
                'formId' => $formId
            ]); ?>
        <?php } ?>
    </div>
</div>

<?php Pjax::end(); ?>

