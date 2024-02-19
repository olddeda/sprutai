<?php

use yii\helpers\Html;

use common\modules\payment\Module as ModulePayment;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $url array */
/* @var $viewPayment string */
/* @var $model \common\modules\content\models\Content */

?>

<div class="grid">
	<div class="col width-100 margin-right-10">
		<?= Html::img($model->author->avatar->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle']) ?>
	</div>
	<div class="col width-auto">
		<div class="author">
			<?= Html::a($model->authorName, $url) ?>
		</div>
		<?php if ($model->author->address) { ?>
			<div class="country margin-top-5">
				<span class="fa fa-globe"></span>
				<?= $model->author->address->country ?>, <?= $model->author->address->city ?>
			</div>
		<?php } ?>
		<?php if ($model->author->telegram && $model->author->telegram->username) { ?>
			<div class="telegram margin-top-5">
				<span class="fa fa-telegram"></span>
				<?= Html::a('@'.$model->author->telegram->username, 'tg://resolve?domain='.$model->author->telegram->username, ['target' => '_blank']) ?>
			</div>
		<?php } ?>
	</div>
</div>

<?php if ($model->author_id != Yii::$app->user->id) { ?>
	<hr>
	<div class="align-center">
		<?php if (Yii::$app->user->id != $model->id) { ?>
			<?= \common\modules\vote\widgets\Subscribe::widget([
				'viewFile' => '@client/views/vote/subscribe_author',
				'entity' => \common\modules\vote\models\Vote::USER_FAVORITE,
				'model' => $model->author,
				'moduleType' => \common\modules\base\helpers\enum\ModuleType::USER,
				'buttonOptions' => [
					'class' => 'vote-subscribe-author',
					'label' => Yii::t('vote', 'button_favorite_author_add'),
					'labelAdd' => Yii::t('vote', 'button_favorite_author_add'),
					'labelRemove' => Yii::t('vote', 'button_favorite_author_remove'),
				],
			]); ?>
		<?php } ?>
	</div>
<?php } ?>