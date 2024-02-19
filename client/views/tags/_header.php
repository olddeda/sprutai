<?php

use yii\helpers\Html;
use yii\widgets\Menu;

use common\modules\telegram\models\TelegramChat;

use common\modules\tag\models\Tag;
use common\modules\tag\models\TagModule;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\tag\models\Tag */

//$style = ($model->background->hasImage()) ? 'background-image: url('.$model->background->getImageSrc(2000, 2000, Mode::RESIZE).');' : '';
//$bgOpacity = ($model->background->hasImage()) ? 'bg-white-opacity' : '';

$style = 'background-color: #fff;';
$bgOpacity = '';

?>

<div class="detail-view-header" style="<?= $style ?>">
	<div class="wrapper-lg <?= $bgOpacity ?>">
		<div class="row m-t">
			<div class="col-sm-7">
				<div class="thumb-lg pull-left m-r">
					<?php if ($model->image->hasImage()) { ?>
					<img class="img-circle" src="<?= $model->image->getImageSrc(90, 90, Mode::CROP_CENTER) ?>" />
					<?php } else { ?>
					<span class="img-circle img-placeholder"><i class="glyphicon glyphicon-picture width-90 height-90"></i></span>
					<?php } ?>
				</div>
				<div class="clear m-b">
					<div class="m-b m-t-sm">
						<h3><?= $model->title ?></h3>
					</div>
					<?php if ($model->telegram) { ?>
						<div class="m-b">
							<?= Html::tag('i', '', ['class' => 'fa fa-telegram']) ?> <?= Html::a(' t.me/'.$model->telegram, 'tg://resolve?domain='.$model->telegram, ['class' => '']) ?>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="col-sm-5 align-right">
				<?= \common\modules\vote\widgets\Subscribe::widget([
					'viewFile' => '@client/views/vote/subscribe_author',
					'entity' => \common\modules\vote\models\Vote::TAG_FAVORITE,
					'model' => $model,
					'moduleType' => \common\modules\base\helpers\enum\ModuleType::TAG,
					'buttonOptions' => [
						'class' => 'vote-btn btn btn-lg vote-subscribe-author',
						'label' => Yii::t('vote', 'button_favorite_tag_add'),
						'labelAdd' => Yii::t('vote', 'button_favorite_tag_add'),
						'labelRemove' => Yii::t('vote', 'button_favorite_tag_remove'),
					],
				]); ?>
            </div>
		</div>
	</div>
</div>
<div class="detail-view-menu">
	<div class="pull-left">
		<?= Menu::widget([
			'options' => [
				'class' => 'nav nav-pills nav-sm',
			],
			'items' => [
				['label' => Yii::t('tag', 'menu_articles'), 'url' => ['/tags/view', 'title' => $model->title]],
				['label' => Yii::t('tag', 'menu_news'), 'url' => ['/tags/news', 'title' => $model->title]],
				['label' => Yii::t('tag', 'menu_blogs'), 'url' => ['/tags/blogs', 'title' => $model->title]],
				['label' => Yii::t('tag', 'menu_projects'), 'url' => ['/tags/projects', 'title' => $model->title]],
				['label' => Yii::t('tag', 'menu_plugins'), 'url' => ['/tags/plugins', 'title' => $model->title]],
				['label' => Yii::t('tag', 'menu_authors'), 'url' => ['/tags/authors', 'title' => $model->title]],
				['label' => Yii::t('tag', 'menu_companies'), 'url' => ['/tags/companies', 'title' => $model->title]],
			],
		]) ?>
	</div>
	<div class="pull-right">
	
	</div>
	<div class="clearfix"></div>
</div>
