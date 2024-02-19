<?php

use common\modules\base\helpers\enum\ModuleType;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\tag\models\search\TagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('seo-module', 'title');

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="seo-module-index">
	
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
					
					// ID
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '60'],
					],
					
					// Module Type
					[
						'attribute' => 'module_type',
						'value' => function ($data) {
							return ModuleType::getLabel($data->module_type);
						},
						'headerOptions' => ['width' => '130'],
					],
					
					// Module Class
					[
						'attribute' => 'module_class',
					],
					
					// Slugify
					[
						'attribute' => 'slugify',
					],
					
					// Actions
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '50',
							'style' => 'text-align:center;'
						],
						'template' => '{update}',
						'buttons' => [
							'update' => function ($url, $model) {
								return Yii::$app->user->can('tag.default.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
									'title' => Yii::t('base', 'button_update'),
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
	
	<?php if (Yii::$app->user->can('seo.module.create')) { ?>
		<div class="form-group margin-top-20">
			<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
				'class' => 'btn btn-primary btn-lg'
			]) ?>
		</div>
	<?php } ?>

</div>
