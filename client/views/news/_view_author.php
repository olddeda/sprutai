<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\payment\Module as ModulePayment;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\News */

?>

<div class="grid">
	<div class="col width-100 margin-right-10">
		<?= Html::img($model->author->avatar->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle']) ?>
	</div>
	<div class="col width-auto">
		<div class="author">
			<?= Html::a($model->authorName, ['news/author', 'id' => $model->author_id]) ?>
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
				<?= Html::a('@'.$model->author->telegram->username, 'https://t.me/'.$model->author->telegram->username, ['target' => '_blank']) ?>
			</div>
		<?php } ?>
	</div>
</div>
<?php $testMode = ModulePayment::getInstance()->gateway_current->testMode; ?>
<?php if (($testMode && Yii::$app->user->getIsAdmin()) || (!$testMode && $model->author_id !== Yii::$app->user->id)) { ?>
	<div class="align-center margin-top-20">
		<?= $this->render('_view_payment', [
			'model' => $model,
		]) ?>
	</div>
<?php } ?>

<hr>
<div class="align-center">
	<?= \common\modules\vote\widgets\Favorite::widget([
		'viewFile' => '@client/views/vote/favorite',
		'entity' => \common\modules\vote\models\Vote::USER_FAVORITE,
		'model' => $model,
		'moduleType' => \common\modules\base\helpers\enum\ModuleType::USER,
		'buttonOptions' => [
			'label' => Yii::t('vote', 'button_favorite_author_add'),
			'labelAdd' => Yii::t('vote', 'button_favorite_author_add'),
			'labelRemove' => Yii::t('vote', 'button_favorite_author_remove'),
		],
	]); ?>
</div>
