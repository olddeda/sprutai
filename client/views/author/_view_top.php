<?php

use yii\bootstrap\Html;

use common\modules\media\helpers\enum\Mode;

?>

<div class="row">
	
	<div class="col-md-12">
		<div class="grid">
			<div class="col width-100 margin-right-10">
				<?= Html::img($model->avatar->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle', 'style' => 'display: block; width:100px !important; height:auto;']) ?>
			</div>
			<div class="col width-auto">
				<div class="author">
					<?= Html::a($model->getFio(), ['/user/profile/view', 'id' => $model->id]) ?>
				</div>
				<?php if ($model->address) { ?>
					<div class="country margin-top-5">
						<span class="fa fa-globe"></span>
						<?= $model->address->country ?>, <?= $model->address->city ?>
					</div>
				<?php } ?>
				<?php if ($model->telegram && $model->telegram->username) { ?>
					<div class="telegram margin-top-5">
						<span class="fa fa-telegram"></span>
						<?= Html::a('@'.$model->telegram->username, 'tg://resolve?domain='.$model->telegram->username, ['target' => '_blank']) ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<div class="align-center margin-top-10">
	<?php if (Yii::$app->user->id != $model->id) { ?>
		<?= \common\modules\vote\widgets\Subscribe::widget([
			'viewFile' => '@client/views/vote/subscribe_author',
			'entity' => \common\modules\vote\models\Vote::USER_FAVORITE,
			'model' => $model,
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

<hr/>
