<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

?>

<div class="content-view-other-view">
	<div class="grid">
		<div class="col width-100 margin-right-10">
			<?= Html::a(Html::img($model->image->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle']), ['/plugins/view', 'id' => $model->id]) ?>

			<div class="votes">
				<div class="vote">
					<?= \common\modules\vote\widgets\Vote::widget([
						'viewFile' => '@client/views/vote/vote',
						'entity' => \common\modules\vote\models\Vote::CONTENT_VOTE,
						'model' => $model,
						'options' => ['class' => 'vote vote-visible-buttons']
					]); ?>
				</div>
			</div>
		</div>
		<div class="col width-auto">
			<div class="title">
				<h5 class="margin-0 margin-bottom-5"><?= Html::a($model->title, ['/plugins/view', 'id' => $model->id]) ?></h5>
			</div>
			<div class="date">
				<?= Yii::$app->formatter->asDate($model->date_at) ?>
			</div>
			<div class="text">
				<?= Html::encode($model->descr) ?>
			</div>
		</div>
	</div>
</div>