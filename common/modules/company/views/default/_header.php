<?php

use yii\helpers\Html;
use yii\widgets\Menu;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\company\models\Company */
?>

<div class="detail-view-header">
	<div class="wrapper-lg">
		<div class="row m-t">
			<div class="col-sm-7">
				<div class="thumb-lg pull-left m-r">
					<?php if ($model->logo->hasImage()) { ?>
					<img class="img-circle" src="<?= $model->logo->getImageSrc(90, 90, Mode::CROP_CENTER) ?>" />
					<?php } else { ?>
					<span class="img-circle img-placeholder"><i class="glyphicon glyphicon-picture width-90 height-90"></i></span>
					<?php } ?>
				</div>
				<div class="clear m-b">
					<div class="m-b m-t-sm">
						<h3><?= $model->title ?></h3>
					</div>
				</div>
			</div>
			<div class="col-sm-5">
            
            </div>
		</div>
	</div>
</div>
<div class="detail-view-menu">
	<?= Menu::widget([
		'options' => [
			'class' => 'nav nav-pills nav-sm',
		],
		'items' => [
			[
				'label' => Yii::t('company', 'menu_general'),
				'url' => ['/company/default/view', 'id' => $model->id],
				'visible' => Yii::$app->user->can('company.default.view'),
			],
			[
				'label' => Yii::t('company', 'menu_portfolio'),
				'url' => ['/company/portfolio/index', 'company_id' => $model->id],
				'visible' => Yii::$app->user->can('company.portfolio.view'),
			],
			[
				'label' => Yii::t('company', 'menu_news'),
				'url' => ['/company/news/index', 'company_id' => $model->id],
				'visible' => Yii::$app->user->can('company.news.index'),
			],
			[
				'label' => Yii::t('company', 'menu_articles'),
				'url' => ['/company/article/index', 'company_id' => $model->id],
				'visible' => Yii::$app->user->can('company.article.index'),
			],
			[
				'label' => Yii::t('company', 'menu_blogs'),
				'url' => ['/company/blog/index', 'company_id' => $model->id],
				'visible' => Yii::$app->user->can('company.blog.index'),
			],
			[
				'label' => Yii::t('company', 'menu_users'),
				'url' => ['/company/user/index', 'company_id' => $model->id],
				'visible' => Yii::$app->user->can('company.user.index'),
			],
			[
				'label' => Yii::t('company', 'menu_discount'),
				'url' => ['/company/discount/index', 'company_id' => $model->id],
				'visible' => Yii::$app->user->can('company.discount.index'),
			],
			[
				'label' => Yii::t('company', 'menu_address'),
				'url' => ['/company/address/index', 'company_id' => $model->id],
				'visible' => Yii::$app->user->can('company.address.index'),
			],
			[
				'label' => Yii::t('company', 'menu_update'),
				'url' => ['/company/default/update', 'id' => $model->id],
				'visible' => Yii::$app->user->can('company.default.update'),
			],
		],
	]) ?>
</div>
