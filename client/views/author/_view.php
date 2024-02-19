<?php

use yii\helpers\Html;
use yii\helpers\Url;

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
				
				<?php if ($model->contentsStat) { ?>
				<div class="content-stats margin-top-10">
					<?php if ($model->contentsStat->articles) { ?>
						<?= Html::a(Yii::t('author', 'count_articles', ['n' => $model->contentsStat->articles]), Url::to(['/user/content/article', 'id' => $model->id]), ['class' => 'btn btn-sm btn-primary inline']) ?>
					<?php } ?>
					<?php if ($model->contentsStat->news) { ?>
						<?= Html::a(Yii::t('author', 'count_news', ['n' => $model->contentsStat->news]), Url::to(['/user/content/news', 'id' => $model->id]), ['class' => 'btn btn-sm btn-primary inline']) ?>
					<?php } ?>
					<?php if ($model->contentsStat->blogs) { ?>
						<?= Html::a(Yii::t('author', 'count_blogs', ['n' => $model->contentsStat->blogs]), Url::to(['/user/content/blog', 'id' => $model->id]), ['class' => 'btn btn-sm btn-primary inline']) ?>
					<?php } ?>
					<?php if ($model->contentsStat->projects) { ?>
						<?= Html::a(Yii::t('author', 'count_projects', ['n' => $model->contentsStat->projects]), Url::to(['/user/content/project', 'id' => $model->id]), ['class' => 'btn btn-sm btn-primary inline']) ?>
					<?php } ?>
					<?php if ($model->contentsStat->plugins) { ?>
						<?= Html::a(Yii::t('author', 'count_plugins', ['n' => $model->contentsStat->plugins]), Url::to(['/user/content/plugin', 'id' => $model->id]), ['class' => 'btn btn-sm btn-primary inline']) ?>
					<?php } ?>
				</div>
				<?php } ?>
				
				<?php if (count($model->tags)) { ?>
					<div class="tags margin-top-10">
						<?php foreach ($model->tags as $tag) { ?>
							<?= Html::a($tag->title, ['/tags/view', 'title' => $tag->title], ['class' => 'btn btn-sm btn-default']) ?>
						<?php } ?>
					</div>
				<?php } ?>
				
			</div>
			<div class="col width-auto align-right">
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
		</div>
	</div>
</div>