<?php

use yii\widgets\Menu;

use common\modules\media\helpers\enum\Mode;

use common\modules\tag\models\Tag;

use common\modules\content\models\Content;
use common\modules\content\models\Portfolio;
use common\modules\content\helpers\enum\Status;
use common\modules\content\helpers\enum\Type as ContentType;

/* @var $this yii\web\View */
/* @var $model \common\modules\company\models\Company */
/* @var $question \common\modules\content\models\Question|null */
/* @var $portfolio \common\modules\content\models\Portfolio|null */

if (!isset($question))
	$question = null;
if (!isset($portfolio))
	$portfolio = null;

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
						<span><?= $model->getTypesName() ?></span>
					</div>
				</div>
			</div>
			<div class="col-sm-5 align-right">
				<?= \common\modules\vote\widgets\Subscribe::widget([
					'viewFile' => '@client/views/vote/subscribe_author',
					'entity' => \common\modules\vote\models\Vote::COMPANY_FAVORITE,
					'model' => $model,
					'moduleType' => \common\modules\base\helpers\enum\ModuleType::COMPANY,
					'buttonOptions' => [
						'class' => 'vote-btn btn btn-lg vote-subscribe-author',
						'label' => Yii::t('vote', 'button_favorite_author_add'),
						'labelAdd' => Yii::t('vote', 'button_favorite_author_add'),
						'labelRemove' => Yii::t('vote', 'button_favorite_author_remove'),
					],
				]); ?>
			</div>
		</div>
	</div>
</div>
<div class="detail-view-menu">
	<?= Menu::widget([
		'options' => [
			'class' => 'nav nav-pills nav-sm',
		],
		'activateParents' => true,
		'items' => [
			[
				'label' => Yii::t('company', 'menu_general'),
				'url' => ['companies/default/view', 'id' => $model->id],
			],
			[
				'label' => Yii::t('company-question', 'title'),
				'url' => ['companies/question/index', 'company_id' => $model->id],
				'activateParents' => true,
				'items' => [
					[
						'url' => ['companies/question/create', 'company_id' => $model->id],
						'options' => ['style' => 'display:none;'],
					],
					[
						'url' => ['companies/question/update', 'company_id' => $model->id, 'id' => ($question ? $question->id : 0)],
						'options' => ['style' => 'display:none;'],
					],
					[
						'url' => ['companies/question/view', 'company_id' => $model->id, 'id' => ($question ? $question->id : 0)],
						'options' => ['style' => 'display:none;'],
					]
				],
			],
			[
				'label' => Yii::t('company', 'menu_portfolio'),
				'url' => ['companies/portfolio/index', 'company_id' => $model->id],
				'visible' => $model->is_integrator && Portfolio::find()->andWhere(['company_id' => $model->id])->count(),
				'activateParents' => true,
				'items' => [
					[
						'url' => ['companies/portfolio/view', 'company_id' => $model->id, 'id' => ($portfolio ? $portfolio->id : 0)],
						'options' => ['style' => 'display:none;'],
					]
				],
			],
			[
				'label' => Yii::t('company', 'menu_news'),
				'url' => ['companies/default/news', 'id' => $model->id],
				'visible' => $model->contentsStat && $model->contentsStat->news,
			],
			[
				'label' => Yii::t('company', 'menu_articles'),
				'url' => ['companies/default/articles', 'id' => $model->id],
				'visible' => $model->contentsStat && $model->contentsStat->articles,
			],
			[
				'label' => Yii::t('company', 'menu_blogs'),
				'url' => ['companies/default/blogs', 'id' => $model->id],
				'visible' => $model->contentsStat && $model->contentsStat->blogs,
			],
			[
				'label' => Yii::t('company', 'menu_projects'),
				'url' => ['companies/default/projects', 'id' => $model->id],
				'visible' => $model->contentsStat && $model->contentsStat->projects,
			],
			[
				'label' => Yii::t('company', 'menu_plugins'),
				'url' => ['companies/default/plugins', 'id' => $model->id],
				'visible' => $model->contentsStat && $model->contentsStat->plugins,
			],
			[
				'label' => Yii::t('company', 'menu_mentions'),
				'url' => ['companies/default/mentions', 'id' => $model->id],
				'visible' => $model->tag_id && Content::find()->joinWith([
					'tags'
				])->where([
					Content::tableName().'.status' => Status::ENABLED,
					Tag::tableName().'.id' => $model->tag_id,
				])->andWhere([
					'in',
					Content::tableName().'.type',
					[ContentType::NEWS, ContentType::ARTICLE, ContentType::BLOG],
				])->count(),
			],
			[
				'label' => Yii::t('company', 'menu_subscribers'),
				'url' => ['companies/default/subscribers', 'id' => $model->id],
				'visible' => ($model->getIsOwn() || Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor()) && $model->contentsStat && $model->contentsStat->subscribers,
			],
		],
	]) ?>
</div>