<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;

use common\modules\base\helpers\enum\Status;

use common\modules\content\helpers\enum\PageType;
use common\modules\content\helpers\TabHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Page */

$this->title = Yii::t('content-page', 'title_view');

//$this->params['breadcrumbs'][] = ['label' => Yii::t('content', 'title'), 'url' => ['/content/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('content-page', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>

<div class="content-page-view">

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
									'attribute' => 'content_id',
									'value' => ($model->parent) ? $model->parent->title : Yii::t('content', 'content_parent_none'),
								],
								[
									'attribute' => 'title',
									'format' => 'raw',
								],
								[
									'attribute' => 'page_type',
									'value' => PageType::getLabel($model->page_type),
								],
								[
									'attribute' => 'text',
									'format' => 'raw',
									'visible' => $model->page_type == PageType::TEXT,
								],
								[
									'attribute' => 'page_path',
									'visible' => $model->page_type == PageType::PATH,
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
								'/user/profile/show',
								'id' => $model->created_by
							], [
								'title' => Yii::t('content-page', 'tooltip_user'),
								'data-toggle' => 'tooltip',
								'data-pjax' => '0',
							]) : '-',
							'format' => 'raw',
						],
						[
							'attribute' => 'updated_by',
							'value' => ($model->updatedBy) ? Html::a($model->updatedBy->fio, [
								'/user/profile/show',
								'id' => $model->updated_by
							], [
								'title' => Yii::t('content-page', 'tooltip_user'),
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
				<?php if (Yii::$app->user->can('content.page.update')) { ?>
					<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('base', 'button_update'), ['update', 'id' => $model->id], [
						'class' => 'btn btn-lg btn-primary'
					]) ?>
				<?php } ?>
				<?php if (Yii::$app->user->can('content.page.index')) { ?>
					<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
						'class' => 'btn btn-default btn-lg'
					]) ?>
				<?php } ?>
			</div>
			<div class="col-md-4 align-right">
				<?php if (Yii::$app->user->can('content.page.delete')) { ?>
					<?= Html::a('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('base', 'button_delete'), ['delete', 'id' => $model->id], [
						'class' => 'btn btn-lg btn-danger',
						'data' => [
							'confirm' => Yii::t('content-page', 'confirm_delete'),
							'method' => 'post',
						],
					]) ?>
				<?php } ?>
			</div>
		</div>
	</div>

</div>
