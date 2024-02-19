<?php

use yii\helpers\Html;
use yii\widgets\Menu;

use common\modules\store\models\Store;
use common\modules\store\models\StoreOrder;

use common\modules\media\helpers\enum\Mode;

$style = ($model->background->hasImage()) ? 'background-image: url('.$model->background->getImageSrc(2000, 2000, Mode::RESIZE).');' : '';
$bgOpacity = ($model->background->hasImage()) ? 'bg-white-opacity' : '';
?>

<div class="detail-view-header" style="<?= $style ?>">
	<div class="wrapper-lg <?= $bgOpacity ?>">
		<div class="row m-t">
			<div class="col-sm-7">
				<div class="thumb-lg pull-left m-r">
					<?php if ($model->avatar->hasImage()) { ?>
					<img class="img-circle" src="<?= $model->avatar->getImageSrc(90, 90, Mode::CROP_CENTER) ?>" />
					<?php } else { ?>
					<span class="img-circle img-placeholder"><i class="glyphicon glyphicon-picture width-90 height-90"></i></span>
					<?php } ?>
				</div>
				<div class="clear m-b">
					<div class="m-b m-t-sm">
						<h3><?= $model->fio ?></h3>
					</div>
					<?php if ($model->email) { ?>
						<p class="m-b">
							<?php if ($model->email) { ?>
								<?= Html::mailto(Html::tag('i', '', ['class' => 'fa fa-envelope']), $model->email , ['class' => 'btn btn-sm btn-bg btn-rounded btn-default btn-icon']) ?>
							<?php } ?>
						</p>
					<?php } ?>
				</div>
			</div>
			<div class="col-sm-5"></div>
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
				'label' => Yii::t('user-profile', 'menu_general'),
				'url' => ($model->id == Yii::$app->user->id ? ['/user/profile/index'] : ['/user/profile/view', 'id' => $model->id]),
				'visible' => Yii::$app->user->can('user.profile.view')
			],
			[
				'label' => Yii::t('user-profile', 'menu_article'),
				'url' => ($model->id == Yii::$app->user->id ? ['/user/content/article'] : ['/user/content/article', 'id' => $model->id]),
				'visible' => Yii::$app->user->can('user.content.article') && \common\modules\content\models\Article::find()->where(['author_id' => $model->id])->count(),
			],
			[
				'label' => Yii::t('user-profile', 'menu_news'),
				'url' => ($model->id == Yii::$app->user->id ? ['/user/content/news'] : ['/user/content/news', 'id' => $model->id]),
				'visible' => Yii::$app->user->can('user.content.news') && \common\modules\content\models\News::find()->where(['author_id' => $model->id])->count(),
			],
			[
				'label' => Yii::t('user-profile', 'menu_blog'),
				'url' => ($model->id == Yii::$app->user->id ? ['/user/content/blog'] : ['/user/content/blog', 'id' => $model->id]),
				'visible' => Yii::$app->user->can('user.content.blog') && \common\modules\content\models\Blog::find()->where(['author_id' => $model->id])->count(),
			],
			[
				'label' => Yii::t('user-profile', 'menu_project'),
				'url' => ($model->id == Yii::$app->user->id ? ['/user/content/project'] : ['/user/content/project', 'id' => $model->id]),
				'visible' => Yii::$app->user->can('user.content.project') && \common\modules\project\models\Project::find()->where(['author_id' => $model->id])->count(),
			],
			[
				'label' => Yii::t('user-profile', 'menu_plugin'),
				'url' => ($model->id == Yii::$app->user->id ? ['/user/content/plugin'] : ['/user/content/plugin', 'id' => $model->id]),
				'visible' => Yii::$app->user->can('user.content.plugin') && \common\modules\plugin\models\Plugin::find()->where(['author_id' => $model->id])->count(),
			],
			[
				'label' => Yii::t('user-profile', 'menu_payments'),
				'url' => ($model->id == Yii::$app->user->id ? ['/user/payment/index'] : ['/user/payment/index', 'id' => $model->id]),
				'visible' => (Yii::$app->user->can('user.payment.index') && ($model->id == Yii::$app->user->id || Yii::$app->user->getIsAdmin())),
			],
			[
				'label' => Yii::t('user-profile', 'menu_accruals'),
				'url' => ($model->id == Yii::$app->user->id ? ['/user/payment/accruals'] : ['/user/payment/accruals', 'id' => $model->id]),
				'visible' => (Yii::$app->user->can('user.payment.accruals') && ($model->id == Yii::$app->user->id || Yii::$app->user->getIsAdmin())),
			],
		],
	]) ?>
</div>
