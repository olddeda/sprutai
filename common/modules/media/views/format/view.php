<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\Boolean;
use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\media\models\MediaFormat */

$this->title = Yii::t('media-format', 'view_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('media', 'title'), 'url' => ['/media/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('media-format', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->format;

?>
<div class="media-format-view">
	
	<div class="row margin-top-20">
		<div class="col-md-12">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'id',
					'width',
					'height',
					[
						'attribute' => 'mode',
						'value' => Mode::getLabel($model->mode),
					],
					[
						'attribute' => 'watermark',
						'value' => Boolean::getLabel($model->watermark),
					],
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
							'title' => Yii::t('company-section', 'tooltip_user'),
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
							'title' => Yii::t('company-section', 'tooltip_user'),
							'data-toggle' => 'tooltip',
							'data-pjax' => '0',
						]) : '-',
						'format' => 'raw',
					],
					'created_at:datetime',
					'updated_at:datetime',
				],
			]) ?>
		</div>
	</div>

	<div class="form-group margin-top-30">
		<div class="row">
			<div class="col-md-8">
				<?php if (Yii::$app->user->can('media.format.update')) { ?>
					<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('base', 'button_update'), ['update', 'id' => $model->id], [
						'class' => 'btn btn-lg btn-primary'
					]) ?>
				<?php } ?>
				<?php if (Yii::$app->user->can('media.format.index')) { ?>
					<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
						'class' => 'btn btn-default btn-lg'
					]) ?>
				<?php } ?>
			</div>
			<div class="col-md-4 align-right">
				<?php if (Yii::$app->user->can('media.format.delete')) { ?>
					<?= Html::a('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('base', 'button_delete'), ['delete', 'id' => $model->id], [
						'class' => 'btn btn-lg btn-danger',
						'data' => [
							'confirm' => Yii::t('media-format', 'confirm_delete'),
							'method' => 'post',
						],
					]) ?>
				<?php } ?>
			</div>
		</div>
	</div>

</div>
