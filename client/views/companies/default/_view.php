<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\company\models\Company */

$isHideAuthor = (isset($hideAuthorName)) ? true : false;
$linkTarget = (isset($urlTarget)) ? $urlTarget : '_self';

$class = 'panel panel-default';
?>

<div class="<?= $class ?>">
	<div class="panel-body">

		<div class="grid">
			<div class="col width-200">
				<?php if ($model->logo->getFileExists()) { ?>
					<div class="image">
						<?= Html::a(Html::img($model->logo->getImageSrc(200, 200, Mode::RESIZE)), ['companies/default/view', 'id' => $model->id], ['target' => $linkTarget]) ?>
					</div>
				<?php } ?>
			</div>
			<div class="col width-auto">
				<div class="row">
					<div class="col-md-8">
						<div class="title margin-bottom-10">
							<?= Html::a($model->title, ['companies/default/view', 'id' => $model->id], ['target' => $linkTarget, 'class' => 'margin-bottom-0']) ?>
							<span><?= $model->getTypesName() ?></span>
						</div>
						<?php if ($model->address) { ?>
						<div class="site margin-bottom-5">
							<span class="fa fa-globe"></span>
							<?= $model->address->country.', '.$model->address->city ?>
						</div>
						<?php } ?>
						<?php if ($model->site) { ?>
						<div class="site margin-bottom-5">
							<span class="fa fa-cloud"></span>
							<?= Html::a($model->site, $model->site, ['target' => '_blank']) ?>
						</div>
						<?php } ?>
						<?php if ($model->email) { ?>
						<div class="email margin-bottom-5">
							<span class="fa fa-envelope"></span>
							<?= Html::mailto($model->email, $model->email) ?>
						</div>
						<?php } ?>
						<?php if ($model->phone) { ?>
						<div class="phone margin-bottom-5">
							<span class="fa fa-phone"></span>
							<?= $model->phone ?>
						</div>
						<?php } ?>
					</div>
					<div class="col-md-4 align-right">
						<?= \common\modules\vote\widgets\Subscribe::widget([
							'viewFile' => '@client/views/vote/subscribe_author',
							'entity' => \common\modules\vote\models\Vote::COMPANY_FAVORITE,
							'model' => $model,
							'moduleType' => \common\modules\base\helpers\enum\ModuleType::COMPANY,
							'buttonOptions' => [
								'class' => 'vote-subscribe-author',
								'label' => Yii::t('vote', 'button_favorite_author_add'),
								'labelAdd' => Yii::t('vote', 'button_favorite_author_add'),
								'labelRemove' => Yii::t('vote', 'button_favorite_author_remove'),
							],
						]); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="text margin-top-10">
			<?= nl2br($model->descr) ?>
		</div>
		
		<?php if ($model->contentsStat) { ?>
			<div class="content-stats margin-top-10">
				<?php if ($model->contentsStat->portfolios) { ?>
					<?= Html::a(Yii::t('company', 'count_portfolios', ['n' => $model->contentsStat->portfolios]), Url::to(['/companies/portfolio/index', 'company_id' => $model->id]), ['class' => 'btn btn-sm btn-primary inline']) ?>
				<?php } ?>
				<?php if ($model->contentsStat->news) { ?>
					<?= Html::a(Yii::t('company', 'count_news', ['n' => $model->contentsStat->news]), Url::to(['news', 'id' => $model->id]), ['class' => 'btn btn-sm btn-primary inline']) ?>
				<?php } ?>
				<?php if ($model->contentsStat->articles) { ?>
					<?= Html::a(Yii::t('company', 'count_articles', ['n' => $model->contentsStat->articles]), Url::to(['articles', 'id' => $model->id]), ['class' => 'btn btn-sm btn-primary inline']) ?>
				<?php } ?>
				<?php if ($model->contentsStat->blogs) { ?>
					<?= Html::a(Yii::t('company', 'count_blogs', ['n' => $model->contentsStat->blogs]), Url::to(['blogs', 'id' => $model->id]), ['class' => 'btn btn-sm btn-primary inline']) ?>
				<?php } ?>
				<?php if ($model->contentsStat->projects) { ?>
					<?= Html::a(Yii::t('company', 'count_projects', ['n' => $model->contentsStat->projects]), Url::to(['projects', 'id' => $model->id]), ['class' => 'btn btn-sm btn-primary inline']) ?>
				<?php } ?>
				<?php if ($model->contentsStat->plugins) { ?>
					<?= Html::a(Yii::t('company', 'count_plugins', ['n' => $model->contentsStat->plugins]), Url::to(['plugins', 'id' => $model->id]), ['class' => 'btn btn-sm btn-primary inline']) ?>
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
		
		<?php if (count($model->discounts)) { ?>
		<?php $discount = current($model->discounts) ?>
		<hr/>
		<div class="discount">
			Компания предоставляет скидку для сообщества в размере <b><?= $discount->getDiscount_all() ?></b> по промокоду <b><?= $discount->promocode ?></b>
		</div>
		<?php } ?>
	</div>
</div>