<?php

use yii\widgets\Pjax;
use yii\helpers\Html;

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

        <ol class="comments-list">
            <?= $this->render('_list', [
				'comments' => $comments,
				'maxLevel' => $maxLevel
			]) ?>
        </ol>

        <?php if (!Yii::$app->user->isGuest) { ?>
            <?= $this->render('_form', [
                'commentModel' => $commentModel,
                'encryptedEntity' => $encryptedEntity,
                'formId' => $formId
            ]); ?>
        <?php } else { ?>
	        <div class="row">
		        <div class="col-sm-12 margin-top-0 margin-bottom-20">
			        <?= Html::a(Yii::t('user', 'link_signin'), ['/user/signin'], [
				        'class' => 'btn btn-primary btn-lg'
			        ]) ?>
		        </div>
	        </div>
	    <?php } ?>
    </div>
</div>

<?php Pjax::end(); ?>

