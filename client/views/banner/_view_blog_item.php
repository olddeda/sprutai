<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

?>

<div class="content-view-other-view">
	<div class="grid">
		<div class="col width-100 margin-right-10">
			<?= Html::a(Html::img($model->image->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle']), ['/blog/view', 'id' => $model->id]) ?>
			<div class="text-primary align-center"><b><?= $model->authorStat->subscribers ?></b></div>
		</div>
		<div class="col width-auto">
			<div class="title">
				<h5 class="margin-0 margin-bottom-5"><?= Html::a($model->title, ['/blog/view', 'id' => $model->id]) ?></h5>
			</div>
			<div class="date">
				<b><?= $model->getAuthorName() ?></b>
			</div>
			<div class="text margin-top-5">
				<?= Html::encode($model->descr) ?>
			</div>
		</div>
	</div>
</div>