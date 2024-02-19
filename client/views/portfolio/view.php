<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\base\components\Helper;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\comments\widgets\CommentWidget;

use common\modules\base\extensions\contentbuilder\ContentBuilderAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderContentAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderSimpleLightBoxAsset;

use common\modules\payment\Module as ModulePayment;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Portfolio */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $useOld bool */

$this->context->layoutContent = 'content_no_panel';

$this->title = \yii\helpers\HtmlPurifier::process($model->title);

$this->params['breadcrumbs'][] = ['url' => 'index', 'label' => Yii::t('portfolio', 'title')];
$this->params['breadcrumbs'][] = \yii\helpers\HtmlPurifier::process($model->title);

$this->seo = $model->seo;


ContentBuilderAsset::register($this);
ContentBuilderContentAsset::register($this);
ContentBuilderSimpleLightBoxAsset::register($this);

$js = <<<JS
    contentbuilderLocalize();

    $('a.is-lightbox').simpleLightbox();

    $('code.code').each(function () {
         codeMirrorHighlight($(this));
    });
JS;
$this->registerJs($js);

$this->registerMetaTag([
	'name' => 'telegram:channel',
	'content' => '@soprut',
]);

$this->registerMetaTag([
	'name' => 'article:published_time',
	'content' => Yii::$app->formatter->asDatetime($model->date_at, 'php:c'),
]);

$this->registerMetaTag([
	'name' => 'article:author',
	'content' => $model->getAuthorName(),
]);

$this->registerMetaTag([
	'name' => 'article:author_url',
	'content' => Url::current([], true),
]);

$this->registerMetaTag([
	'name' => 'article:descr',
	'content' => $model->descr,
]);

if ($model->image->getFileExists()) {
	$this->registerMetaTag([
		'name' => 'og:image',
		'content' => Url::to($model->image->getImageSrc(1000, 400, Mode::RESIZE), true),
	]);
	$this->registerMetaTag([
		'name' => 'og:image:width',
		'content' => 1000,
	]);
	$this->registerMetaTag([
		'name' => 'og:image:height',
		'content' => 400,
	]);
}

?>

<div class="content-view">
	
	<?= Html::img(Url::to($model->image->getImageSrc(1000, 400, Mode::RESIZE), true), ['class' => 'preview hidden']) ?>
	
	<div class="row">
		<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
			<div class="panel panel-default">
				<div class="panel-body">
					<article>
						<?= $this->render('_view_text', [
							'model' => $model,
						]); ?>
					</article>
				</div>
			</div>

			<div class="votes">
				<?= $this->render('//statistics/_visit_panel', [
					'model' => $model,
				]) ?>
				
				<div class="vote panel panel-default">
					<div class="panel-body">
						<?= \common\modules\vote\widgets\Vote::widget([
							'viewFile' => '@client/views/vote/vote',
							'entity' => \common\modules\vote\models\Vote::CONTENT_VOTE,
							'model' => $model,
							'moduleType' => ModuleType::CONTENT,
							'options' => ['class' => 'vote vote-visible-buttons']
						]); ?>
					</div>
				</div>

				<div class="favorite panel panel-default">
					<div class="panel-body">
						<?= \common\modules\vote\widgets\Favorite::widget([
							'viewFile' => '@client/views/vote/favorite',
							'entity' => \common\modules\vote\models\Vote::CONTENT_FAVORITE,
							'model' => $model,
							'moduleType' => ModuleType::CONTENT,
						]); ?>
					</div>
				</div>
				
			</div>
			
			<?php if ($model->getTags()->count()) { ?>
			<div class="tags margin-bottom-15">
				<?php foreach ($model->tags as $tag) { ?>
					<?= Html::a($tag->title, ['/tags/view', 'title' => $tag->title], ['class' => 'btn btn-primary']) ?>
				<?php } ?>
			</div>
			<?php } ?>

			<div class="content-comments">
				<?= common\modules\comments\widgets\CommentWidget::widget([
					'moduleType' => ModuleType::CONTENT,
					'model' => $model,
					'commentView' => '@client/views/comments/index',
					'relatedTo' => Yii::t('comments', 'related_to_text', [
						'title' => $model->title,
						'url' => Url::current(),
					]),
				]); ?>
			</div>
			
			<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> К списку работ', ['index'], [
				'class' => 'btn btn-default btn-lg margin-bottom-15'
			]) ?>
			
		</div>
		<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
			<?= $this->render('//banner/view', ['showLeaders' => false]) ?>
			
			<div class="panel panel-default">
				<div class="panel-body">
					<?php if ($model->company_id) { ?>
						<?= $this->render('/companies/default/_block', [
							'model' => $model,
							'url' => ['/companies/portfolio/index', 'company_id' => $model->company_id],
						]) ?>
					<?php } else { ?>
						<?= $this->render('/author/_block', [
							'model' => $model,
							'url' => ['/user/content/article', 'id' => $model->author_id],
							'viewPayment' => '//article/_view_payment',
						]) ?>
					<?php } ?>
				</div>
			</div>
			
			<?= $this->render('//telegram/_block', [
				'model' => $model,
			]) ?>
			
			<?= $this->render('//companies/top/_block') ?>
			
			<?php if ($dataProvider->count) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="margin-0 text-primary"><?= Yii::t('article', 'header_similar') ?></h4>
				</div>
				<div class="panel-body">
					<?= $this->render('_view_other', [
						'model' => $model,
						'dataProvider' => $dataProvider,
					]) ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	
</div>
