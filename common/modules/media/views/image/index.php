<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\company\models\Company;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\media\models\MediaImageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('media-image', 'title');
$this->params['breadcrumbs'][] = ['label' => Yii::t('media', 'title'), 'url' => ['/media/default/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="media-image-index">
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
						'attribute' => 'image',
						'format' => 'html',
						'headerOptions' => ['width' => '110'],
						'value' => function ($data) {
							$str = Html::beginTag('div', [
								'class' => 'thumbnail margin-bottom-0',
							]);
							$str .= Html::img($data->getImageSrc(100, 100, Mode::CROP_CENTER), [
								'width' => '100px',
								'height' => '100px',
							]);
							$str .= Html::endTag('div');
							return $str;
						},
					],
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '50'],
					],
					[
						'filter' => ModuleType::listData(),
						'attribute' => 'module_type',
						'value' => function($data) {
							return ModuleType::getLabel($data->module_type);
						},
						'headerOptions' => ['width' => '150'],
					],
					[
						'attribute' => 'module_id',
						'headerOptions' => ['width' => '100'],
					],
					'title',
					[
						'attribute' => 'width_and_height',
						'headerOptions' => ['width' => '100'],
					],
					[
						'attribute' => 'size',
						'headerOptions' => ['width' => '100'],
					],
					[
						'filter' => [
							Status::ENABLED => Status::getLabel(Status::ENABLED),
							Status::DELETED => Status::getLabel(Status::DELETED),
						],
						'attribute' => 'status',
						'value' => function($data) {
							return Status::getLabel($data->status);
						},
						'headerOptions' => ['width' => '120'],
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '35',
							'style' => 'text-align:center;'
						],
						'template' => '{view}',
						'buttons' => [
							'view' => function ($url, $model) {
								return Yii::$app->user->can('media.image.view') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
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

</div>
