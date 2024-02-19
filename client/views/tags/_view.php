<?php

use yii\helpers\Html;
use yii\helpers\Url;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model \common\modules\tag\models\Tag */

?>

<div class="panel panel-default">
	<div class="panel-body">
		<div class="grid">
			<div class="col width-100 margin-right-10">
				<?= Html::a(Html::img($model->image->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle']), ['view', 'title' => $model->title]) ?>
			</div>
			<div class="col width-auto">
				<div class="author">
					<?= Html::a($model->title, ['view', 'title' => $model->title]) ?>
				</div>
				<div class="descr">
					<?= $model->descr ?>
				</div>
			</div>
			<div class="col width-auto align-right">
				<?= \common\modules\vote\widgets\Subscribe::widget([
					'viewFile' => '@client/views/vote/subscribe_author',
					'entity' => \common\modules\vote\models\Vote::TAG_FAVORITE,
					'model' => $model,
					'moduleType' => \common\modules\base\helpers\enum\ModuleType::TAG,
					'buttonOptions' => [
						'class' => 'vote-subscribe-author',
						'label' => Yii::t('vote', 'button_favorite_tag_add'),
						'labelAdd' => Yii::t('vote', 'button_favorite_tag_add'),
						'labelRemove' => Yii::t('vote', 'button_favorite_tag_remove'),
					],
				]); ?>
			</div>
		</div>
	</div>
</div>