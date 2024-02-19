<?php

use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model \common\modules\user\models\User */

?>

<div class="panel panel-default">
	<div class="panel-body">
		<div class="grid">
			<div class="col width-100 margin-right-10">
				<?= Html::img($model->avatar->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle']) ?>
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
			<div class="col width-auto align-right"></div>
		</div>
	</div>
</div>