<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

use common\modules\base\extensions\contentbuilder\ContentBuilderAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderContentAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderSimpleLightBoxAsset;

use common\modules\content\helpers\enum\Status;

use common\modules\media\widgets\show\ImageShowWidget;
use common\modules\media\helpers\enum\Mode;


/* @var $this yii\web\View */
/* @var $company common\modules\company\models\Company */
/* @var $model common\modules\content\models\News */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('content-news', 'title_view');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['/company/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/company/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('content-news', 'title'), 'url' => ['index', 'company_id' => $company->id]];
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
JS;
$this->registerJs($js);
?>

<div class="content-view">
	
	<?= Html::img(Url::to($model->image->getImageSrc(1000, 400, Mode::RESIZE), true), ['class' => 'preview hidden']) ?>

	<div class="row">
		<div class="col-sx-12 col-sm-12 col-md-12 col-lg-9">
			<div class="panel panel-default">
				<div class="panel-body">
					<news>
						<?= $this->render('_view_text', [
							'model' => $model,
							'field' => 'text',
						]); ?>
					</news>
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
						<?php if (Yii::$app->user->can('content.news.update')) { ?>
							<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('base', 'button_update'), ['update', 'company_id' => $company->id, 'id' => $model->id], [
								'class' => 'btn btn-lg btn-primary'
							]) ?>
						<?php } ?>
						<?php if (Yii::$app->user->can('content.news.index')) { ?>
							<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index', 'company_id' => $company->id,], [
								'class' => 'btn btn-default btn-lg'
							]) ?>
						<?php } ?>
					</div>
					<div class="col-md-4 align-right">
						<?php if (Yii::$app->user->can('content.news.delete')) { ?>
							<?= Html::a('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('base', 'button_delete'), ['delete', 'company_id' => $company->id, 'id' => $model->id], [
								'class' => 'btn btn-lg btn-danger',
								'data' => [
									'confirm' => Yii::t('content-news', 'confirm_delete'),
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
												return Html::a($data->getAuthorName(), ['user/profile-view', 'id' => $data->author_id]);
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
										'title' => Yii::t('content-news', 'tooltip_user'),
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
										'title' => Yii::t('content-news', 'tooltip_user'),
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
		</div>
	</div>
</div>
