<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\base\extensions\editable\EditableColumn;
use common\modules\base\extensions\select2\Select2;

use \common\modules\base\helpers\enum\Boolean;
use common\modules\base\helpers\enum\Status;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\menu\models\search\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('menu', 'title');

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="menu-index">
	
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
					
					// Title
					[
						'class' => EditableColumn::class,
						'attribute' => 'title',
						'url' => ['editable'],
						'editableOptions' => [
							'title' => Yii::t('menu', 'editable_title'),
						],
					],
					
					// Visible
					[
						'class' => EditableColumn::class,
						'type' => 'select2',
						'attribute' => 'visible',
						'label' => Yii::t('menu', 'field_visible_short'),
						'value' => function($data) {
							return Boolean::getLabel($data->visible);
						},
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'visible',
							'items' => Boolean::listData(),
							'clientOptions' => [
								'allowClear' => true,
								'hideSearch' => true,
								'placeholder' => '',
							],
						]),
						'url' => ['editable'],
						'editableOptions' => function ($data) {
							return [
								'title' => Yii::t('menu', 'editable_visible'),
								'source' => Boolean::listData(),
								'value' => $data->visible,
							];
						},
						'pluginOptions' => [
							'select2' => [
								'width' => 150,
								'hideSearch' => true,
							],
						],
						'headerOptions' => ['width' => '150'],
					],
					
					
					// Status
					[
						'class' => EditableColumn::class,
						'type' => 'select2',
						'attribute' => 'status',
						'value' => function($data) {
							return Status::getLabel($data->status);
						},
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'status',
							'items' => Status::listData(),
							'clientOptions' => [
								'allowClear' => true,
								'hideSearch' => true,
								'placeholder' => '',
							],
						]),
						'url' => ['editable'],
						'editableOptions' => function ($data) {
							return [
								'title' => Yii::t('menu', 'editable_status'),
								'source' => Status::listData(),
								'value' => $data->status,
							];
						},
						'pluginOptions' => [
							'select2' => [
								'width' => 150,
								'hideSearch' => true,
							],
						],
						'headerOptions' => ['width' => '150'],
					],
					
					// Actions
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '75',
							'style' => 'text-align:center;'
						],
						'template' => '{view} {update} {delete}',
						'buttons' => [
							'view' => function ($url, $model) {
								return Yii::$app->user->can('menu.default.view') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'update' => function ($url, $model) {
								return Yii::$app->user->can('menu.default.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
									'title' => Yii::t('base', 'button_update'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'delete' => function ($url, $model) {
								return Yii::$app->user->can('menu.default.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
									'title' => Yii::t('base', 'button_delete'),
									'data-method' => 'POST',
									'data-confirm' => Yii::t('menu', 'confirm_delete_name', ['title' => $model->title]),
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

	<?php if (Yii::$app->user->can('menu.default.create')) { ?>
	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>
	<?php } ?>

</div>
