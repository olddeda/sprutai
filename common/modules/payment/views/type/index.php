<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\base\extensions\editable\EditableColumn;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\Boolean;

use common\modules\payment\helpers\enum\Kind;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\payment\models\search\PaymentTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('payment-type', 'title');

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="content-article-index">
	
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
					
					// Id
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '70'],
					],
					
					// Kind
					[
						'attribute' => 'kind',
						'value' => function($data) {
							return Kind::getLabel($data->kind);
						},
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'kind',
							'items' => Kind::listData(),
							'clientOptions' => [
								'allowClear' => true,
								'hideSearch' => true,
								'placeholder' => '',
							],
						]),
						'headerOptions' => ['width' => '125'],
					],
					
					// Title
					[
						'class' => EditableColumn::class,
						'attribute' => 'title',
						'url' => ['editable'],
						'editableOptions' => [
							'title' => Yii::t('payment-type', 'editable_title'),
						],
					],
					
					// Price
					[
						'class' => EditableColumn::class,
						'attribute' => 'price',
						'url' => ['editable'],
						'editableOptions' => [
							'title' => Yii::t('payment-type', 'editable_price'),
						],
						'headerOptions' => ['width' => '200'],
					],
					
					// Price Tax
					[
						'class' => EditableColumn::class,
						'attribute' => 'price_tax',
						'url' => ['editable'],
						'editableOptions' => function($data) {
							return [
								'title' => Yii::t('payment-type', 'editable_price_tax'),
							];
						},
						'headerOptions' => ['width' => '200'],
					],
					
					/*
					// Price Fixed
					[
						'class' => EditableColumn::class,
						'type' => 'select2',
						'attribute' => 'price_fixed',
						'value' => function($data) {
							return Boolean::getLabel($data->price_fixed);
						},
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'price_fixed',
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
								'title' => Yii::t('payment-type', 'editable_price_fixed'),
								'source' => Boolean::listData(),
								'hideSearch' => true,
								'value' => $data->physical,
							];
						},
						'pluginOptions' => [
							'select2' => [
								'width' => 160,
								'hideSearch' => true,
							],
						],
						'headerOptions' => ['width' => '150'],
					],
					*/
					
					// Physical
					[
						'class' => EditableColumn::class,
						'type' => 'select2',
						'attribute' => 'physical',
						'value' => function($data) {
							return Boolean::getLabel($data->physical);
						},
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'physical',
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
								'title' => Yii::t('payment-type', 'editable_physical'),
								'source' => Boolean::listData(),
								'hideSearch' => true,
								'value' => $data->physical,
							];
						},
						'pluginOptions' => [
							'select2' => [
								'width' => 160,
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
								'title' => Yii::t('payment-type', 'editable_status'),
								'source' => Status::listData(),
								'hideSearch' => true,
								'value' => $data->status,
							];
						},
						'pluginOptions' => [
							'select2' => [
								'width' => 160,
								'hideSearch' => true,
							],
						],
						'headerOptions' => ['width' => '150'],
					],
					
					// Actions
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '50',
							'style' => 'text-align:center;'
						],
						'template' => '{update} {delete}',
						'buttons' => [
							'view' => function ($url, $model) {
								return Yii::$app->user->can('payment.type.view') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'update' => function ($url, $model) {
								return  Yii::$app->user->can('payment.type.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
									'title' => Yii::t('base', 'button_update'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'delete' => function ($url, $model) {
								return Yii::$app->user->can('payment.type.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
									'title' => Yii::t('base', 'button_delete'),
									'data-method' => 'POST',
									'data-confirm' => Yii::t('payment-type', 'confirm_delete_name', ['title' => $model->title]),
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

	<?php if (Yii::$app->user->can('payment.type.create')) { ?>
	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>
	<?php } ?>

</div>
