<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\payment\Module as ModulePayment;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Article */

?>

<div class="grid">
	<div class="col width-100 margin-right-10">
		<?= Html::img($model->author->avatar->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle']) ?>
	</div>
	<div class="col width-auto">
		<div class="author">
			<?= Html::a($model->authorName, ['plugins/author', 'id' => $model->author_id]) ?>
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