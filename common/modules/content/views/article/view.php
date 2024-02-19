<?php

use common\modules\base\extensions\contentbuilder\ContentBuilderAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderContentAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderSimpleLightBoxAsset;
use common\modules\content\helpers\enum\Status;
use common\modules\media\helpers\enum\Mode;
use common\modules\media\widgets\show\ImageShowWidget;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;


/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Article */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('content-article', 'title_view');

//$this->params['breadcrumbs'][] = ['label' => Yii::t('content', 'title'), 'url' => ['/content/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('content-article', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

ContentBuilderAsset::register($this);
ContentBuilderContentAsset::register($this);
ContentBuilderSimpleLightBoxAsset::register($this);

$js = <<<JS
    contentbuilderLocalize();

    $('a.is-lightbox').simpleLightbox();

    $('code.code').each(function () {
         codeMirrorHighlight($(this));
    });
    
    $('a.tab_link').click(function() {
        $('a.tab_link').parent().removeClass('active');
        $(this).parent().addClass('active');
        
        $('#article_text').addClass('hide');
        $('#article_text_new').addClass('hide');
        $('#article_' + $(this).data('type')).removeClass('hide');
    });
JS;
$this->registerJs($js);
?>

<script type="application/ld+json">
{
	"@context": "https://schema.org",
	"@type": "Article",
	"mainEntityOfPage": {
		"@type": "WebPage",
		"@id": "https://example.com/my-news-article"
	},
	"headline": "Article headline",
	"image": [
		"https://patrickcoombe.com/wp-content/uploads/2014/05/patrick-coombe.jpg",
		"https://example.com/photos/4x3/photo.jpg",
		"https://example.com/photos/16x9/photo.jpg"
	],
	"datePublished": "2019-01-05T08:00:00+08:00",
	"dateModified": "2019-01-05T09:20:00+08:00",
	"author": {
		"@type": "Person",
		"name": "Patrick Coombe"
	},
	"publisher": {
		"@type": "Organization",
		"name": "Elite Strategies",
		"logo": {
			"@type": "ImageObject",
			"url": "https://elitestrategies-elitestrategies.netdna-ssl.com/wp-content/uploads/2013/04/elitestrategies.png"
		}
	},
	"description": "A most wonderful article"
}
</script>

<div class="content-view">
	
	<?= Html::img(Url::to($model->image->getImageSrc(1000, 400, Mode::RESIZE), true), ['class' => 'preview hidden']) ?>

	<div class="row">
		<div class="col-sx-12 col-sm-12 col-md-12 col-lg-9">

			<div class="panel panel-default">
				<div class="panel-body">

					<article id="article_text">
						<?= $this->render('_view_text', [
							'model' => $model,
							'field' => 'text'
						]); ?>
					</article>
				</div>
			</div>
			
			<?php if ($model->getTags()->count()) { ?>
				<div class="tags margin-bottom-15">
					<?php foreach ($model->tags as $tag) { ?>
						<?= Html::a($tag->title, ['/tags/view', 'title' => $tag->title], ['class' => 'btn btn-primary']) ?>
					<?php } ?>
				</div>
			<?php } ?>

			<div class="form-group margin-top-10 margin-bottom-0">
				<div class="row">
					<div class="col-md-8">
						<?php if (Yii::$app->user->can('content.article.update')) { ?>
							<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('base', 'button_update'), ['update', 'id' => $model->id], [
								'class' => 'btn btn-lg btn-primary'
							]) ?>
						<?php } ?>
						<?php if (Yii::$app->user->can('content.article.index')) { ?>
							<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
								'class' => 'btn btn-default btn-lg'
							]) ?>
						<?php } ?>
					</div>
					<div class="col-md-4 align-right">
						<?php if (Yii::$app->user->can('content.article.delete')) { ?>
							<?= Html::a('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('base', 'button_delete'), ['delete', 'id' => $model->id], [
								'class' => 'btn btn-lg btn-danger',
								'data' => [
									'confirm' => Yii::t('content-article', 'confirm_delete'),
									'method' => 'post',
								],
							]) ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sx-12 col-sm-12 col-md-12 col-lg-3">
			<div class="panel panel-default">
				<div class="panel-body">
					<fieldset class="margin-bottom-0">
						<legend><?= Yii::t('content', 'header_general') ?></legend>
		
						<div class="row margin-top-15">
							<div class="col-md-12">
								<?= DetailView::widget([
									'model' => $model,
									'options' => [
										'class' => 'table table-striped detail-view',
									],
									'attributes' => [
										[
											'attribute' => 'image',
											'format' => 'html',
											'headerOptions' => ['width' => '90'],
											'value' => function ($data) {
												return ImageShowWidget::widget([
													'model' => $data,
													'width' => 80,
													'height' => 80,
													'mode' => Mode::CROP_CENTER,
												]);
											},
										],
										'date_at:datetime',
										'published_at:datetime',
									],
								]) ?>
							</div>
						</div>
					</fieldset>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">
					<fieldset class="margin-bottom-0">
						<legend><?= Yii::t('content', 'header_author') ?></legend>

						<div class="row margin-top-15">
							<div class="col-md-12">
								<?= DetailView::widget([
									'model' => $model,
									'options' => [
										'class' => 'table table-striped detail-view',
									],
									'attributes' => [
										[
											'attribute' => 'image',
											'label' => Yii::t('user', 'field_avatar'),
											'format' => 'html',
											'headerOptions' => ['width' => '90'],
											'value' => function ($data) {
												return ImageShowWidget::widget([
													'model' => $data->author->avatar,
													'width' => 80,
													'height' => 80,
													'mode' => Mode::CROP_CENTER,
												]);
											},
										],
										[
											'attribute' => 'author_id',
											'label' => Yii::t('user', 'field_fio'),
											'value' => function ($data) {
												return Html::a($data->getAuthorName(), ['/user/profile/view', 'id' => $data->author_id]);
											},
											'format' => 'raw',
										],
										[
											'attribute' => 'country_city',
											'label' => Yii::t('user', 'field_address_country_city'),
											'value' => function ($data) {
												if ($data->author->address) {
													return $data->author->address->country.', '.$data->author->address->city;
												}
												return Html::tag('span', Yii::t('base', 'empty'), ['class' => 'text-danger']);
											},
											'format' => 'raw',
										],
										[
											'attribute' => 'telegram',
											'label' => Yii::t('user', 'field_telegram'),
											'value' => function ($data) {
												if ($data->author->telegram) {
													return Html::a('@'.$data->author->telegram->username, 'tg://resolve?domain='.$data->author->telegram->username);
												}
												return Html::tag('span', Yii::t('base', 'empty'), ['class' => 'text-danger']);
											},
											'format' => 'raw',
										],
									],
								]) ?>
							</div>
						</div>
					</fieldset>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">
					<fieldset class="margin-bottom-0">
						<legend><?= Yii::t('content', 'header_other') ?></legend>
						
						<?= DetailView::widget([
							'model' => $model,
							'options' => [
								'class' => 'table table-striped detail-view',
							],
							'attributes' => [
								'id',
								[
									'attribute' => 'status',
									'value' => Status::getLabel($model->status),
								],
								[
									'attribute' => 'created_by',
									'value' => ($model->createdBy) ? Html::a($model->createdBy->fio, [
										'/user/profile/view',
										'id' => $model->created_by
									], [
										'title' => Yii::t('content-article', 'tooltip_user'),
										'data-toggle' => 'tooltip',
										'data-pjax' => '0',
									]) : '-',
									'format' => 'raw',
								],
								[
									'attribute' => 'updated_by',
									'value' => ($model->updatedBy) ? Html::a($model->updatedBy->fio, [
										'/user/profile/view',
										'id' => $model->updated_by
									], [
										'title' => Yii::t('content-article', 'tooltip_user'),
										'data-toggle' => 'tooltip',
										'data-pjax' => '0',
									]) : '-',
									'format' => 'raw',
								],
								'created_at:datetime',
								'updated_at:datetime',
							],
						]) ?>
					</fieldset>
				</div>
			</div>

			<?php if ($unique = $model->unique) { ?>
			<div class="panel panel-default">
				<div class="panel-body">
					<fieldset class="margin-bottom-0">
						<legend><?= Yii::t('content-unique', 'header_antiplagiat') ?></legend>
						
						<?= DetailView::widget([
							'model' => $unique,
							'options' => [
								'class' => 'table table-striped detail-view',
							],
							'attributes' => [
								[
									'attribute' => 'unique',
									'value' => $unique->unique ? Yii::$app->formatter->asPercent($unique->unique) : Yii::t('content-unique', 'checked'),
								],
								[
									'attribute' => 'spam_percent',
									'value' => $unique->spam_percent ? Yii::$app->formatter->asPercent($unique->spam_percent / 100) : Yii::t('content-unique', 'checked'),
								],
								[
									'attribute' => 'water_percent',
									'value' => $unique->water_percent ? Yii::$app->formatter->asPercent($unique->water_percent / 100) : Yii::t('content-unique', 'checked'),
								],
								[
									'attribute' => 'count_chars_with_space',
									'value' => $unique->count_chars_with_space ?: Yii::t('content-unique', 'checked'),
								],
								[
									'attribute' => 'count_chars_without_space',
									'value' => $unique->count_chars_without_space ?: Yii::t('content-unique', 'checked'),
								],
								[
									'attribute' => 'count_words',
									'value' => $unique->count_words ?: Yii::t('content-unique', 'checked'),
								],
								[
									'attribute' => 'spellcheck',
									'value' => $unique->spellcheck ? Yii::t('content-unique', 'count_errors', ['n' => count($unique->spellcheck)]) : Yii::t('content-unique', 'checked'),
								],
							],
						]) ?>

						<div class="pull-right">
							<a href="https://text.ru/antiplagiat/<?=$unique->uid ?>" class="margin-top-10 btn btn-primary" target="_blank">Смотреть на text.ru</a>
						</div>
						
					</fieldset>
				</div>
			</div>
			
			<?php if ($unique->urls) { ?>
			<div class="panel panel-default">
				<div class="panel-body">
					<fieldset class="margin-bottom-0">
						<legend><?= Yii::t('content-unique', 'header_duplicates') ?></legend>
						
						<?= GridView::widget([
							'dataProvider' => new ArrayDataProvider([
								'allModels' => $unique->urls,
							]),
							'tableOptions' => [
								'class' => 'table table-striped'
							],
							'showHeader' => false,
							'showFooter' => false,
							'summary' => false,
							'columns' => [
								[
									'attribute' => 'url',
								],
								[
									'attribute' => 'plagiat',
									'value' => function($data) {
										return Yii::$app->formatter->asPercent($data['plagiat'] / 100);
									}
								],
							],
						]);
						?>
					</fieldset>
				</div>
			</div>
			<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>
