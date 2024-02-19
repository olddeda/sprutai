<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use common\modules\content\helpers\enum\Status;

use common\modules\media\widgets\show\ImageShowWidget;
use common\modules\media\helpers\enum\Mode;

use common\modules\rbac\helpers\enum\Role;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Article */

$this->title = Yii::t('content-article', 'title_view');

//$this->params['breadcrumbs'][] = ['label' => Yii::t('content', 'title'), 'url' => ['/content/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('content-article', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>

<div class="content-article-view">
	
	<div class="row margin-top-20">
		<div class="col-md-12">

			<fieldset>
				<legend><?= Yii::t('content', 'header_general') ?></legend>

				<div class="row margin-top-15">
					<div class="col-md-12">
						<?= DetailView::widget([
							'model' => $model,
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
								[
									'attribute' => 'title',
								],
								[
									'attribute' => 'date',
								],
								[
									'attribute' => 'tags',
									'value' => $model->getTagsValues(),
								],
								[
									'attribute' => 'descr',
								],
								[
									'attribute' => 'text',
									'format' => 'raw',
								],
								[
									'attribute' => 'source_name',
								],
								[
									'attribute' => 'source_url',
								],
							],
						]) ?>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend><?= Yii::t('content', 'header_other') ?></legend>
				
				<?= DetailView::widget([
					'model' => $model,
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

	<div class="form-group margin-top-30">
		<div class="row">
			<div class="col-md-8">
				<?php if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]) && !in_array($model->status, [Status::MODERATED, Status::ENABLED])) { ?>
				<?php if (Yii::$app->user->can('content.article.update')) { ?>
					<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('base', 'button_update'), ['update', 'id' => $model->id], [
						'class' => 'btn btn-lg btn-primary'
					]) ?>
				<?php } ?>
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
