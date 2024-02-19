<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\Boolean;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\media\models\MediaFormat */

$this->title = Yii::t('media-format', 'view_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('media', 'title'), 'url' => ['/media/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('media-image', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id;

?>
<div class="media-image-view">
	<div class="row margin-top-20">
		<div class="col-md-12">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					[
						'format' => ['image', ['width' => 300, 'height' => 300]],
						'attribute' => 'image',
						'value' => $model->getImageSrc(300, 300, Mode::CROP_CENTER),
					],
					'id',
					[
						'attribute' => 'module_type',
						'value' => ModuleType::getLabel($model->module_type),
					],
					'module_id',
					'width',
					'height',
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

	<div class="form-group margin-top-20">
		<div class="row">
			<div class="col-md-8">
				<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
					'class' => 'btn btn-default btn-lg'
				]) ?>
			</div>
		</div>
	</div>

</div>