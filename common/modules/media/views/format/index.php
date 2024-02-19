<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\Boolean;
use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\media\models\MediaFormatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('media-format', 'title');
$this->params['breadcrumbs'][] = ['label' => Yii::t('media', 'title'), 'url' => ['/media/default/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="media-format-index">
	<div class="row margin-top-20">
		<div class="col-md-12">
			<?php Pjax::begin([
				'timeout' => 10000,
				'enablePushState' => false
			]); ?>
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'tableOptions' => [
					'class' => 'table table-striped'
				],
				'columns' => [
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '50'],
					],
					'format',
					[
						'attribute' => 'width',
						'headerOptions' => ['width' => '100'],
					],
					[
						'attribute' => 'height',
						'headerOptions' => ['width' => '100'],
					],
					[
						'filter' => Mode::listData(),
						'attribute' => 'mode',
						'value' => function($data) {
							return Mode::getLabel($data->mode);
						},
						'headerOptions' => ['width' => '150'],
					],
					[
						'filter' => Boolean::listData(),
						'attribute' => 'watermark',
						'value' => function($data) {
							return Boolean::getLabel($data->watermark);
						},
						'headerOptions' => ['width' => '150'],
					],
					[
						'filter' => Status::listData(),
						'attribute' => 'status',
						'value' => function($data) {
							return Status::getLabel($data->status);
						},
						'headerOptions' => ['width' => '150'],
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '70',
							'style' => 'text-align:center;'
						],
						'buttons' => [
							'view' => function ($url, $model) {
								return Yii::$app->user->can('media.format.view') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'update' => function ($url, $model) {
								return Yii::$app->user->can('media.format.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
									'title' => Yii::t('base', 'button_update'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'delete' => function ($url, $model) {
								return Yii::$app->user->can('media.format.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
									'title' => Yii::t('base', 'button_delete'),
									'data-method' => 'POST',
									'data-confirm' => Yii::t('media-format', 'confirm_delete_name', ['title' => $model->format]),
									'data-pjax' => '1',
									'data-toggle' => 'tooltip',
								]) : '';
							},
						],
					],
				],
			]); ?>
			<?php Pjax::end(); ?>
		</div>
	</div>

	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>

</div>
