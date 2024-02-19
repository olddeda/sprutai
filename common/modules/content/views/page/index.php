<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\base\extensions\editable\EditableColumn;
use common\modules\base\extensions\select2\Select2;

use common\modules\base\extensions\datetimepicker\DateTimePicker;
use common\modules\base\helpers\enum\Status;

use common\modules\content\models\Page;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\content\models\search\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('content-page', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('content', 'title'), 'url' => ['/content/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-page-index">
	
	<div class="row margin-top-20">
		<div class="col-md-12">
			<?php Pjax::begin([
				'timeout' => 10000,
				'enablePushState' => true
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
					[
						'class' => EditableColumn::className(),
						'type' => 'select2',
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'content_id',
							'items' => Page::treeListData(),
							'clientOptions' => [
								'allowClear' => true,
								'hideSearch' => true,
								'placeholder' => '',
							],
						]),
						'attribute' => 'content_id',
						'value' => function ($data) {
							return ($data->parent) ? $data->parent->title : Yii::t('content', 'content_parent_none');
						},
						'url' => ['editable'],
						'editableOptions' => function ($data) {
							return [
								'title' => Yii::t('content', 'editable_content_id'),
								'source' => Page::treeListData(),
								'value' => $data->content_id,
							];
						},
						'pluginOptions' => [
							'select2' => [
								'width' => 200,
								'allowClear' => true,
								'hideSearch' => true,
								'placeholder' => '',
							],
						],
						'headerOptions' => ['width' => '250'],
					],
					[
						'class' => EditableColumn::className(),
						'attribute' => 'title',
						'url' => ['editable'],
						'editableOptions' => [
							'title' => Yii::t('content', 'editable_title'),
						],
						'format' => 'raw',
					],
					[
						'class' => EditableColumn::className(),
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
								'title' => Yii::t('content', 'editable_status'),
								'source' => Status::listData(),
								'hideSearch' => true,
								'value' => $data->status,
							];
						},
						'pluginOptions' => [
							'select2' => [
								'width' => 150,
								'hideSearch' => true,
							],
						],
						'headerOptions' => ['width' => '140'],
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '70',
							'style' => 'text-align:center;'
						],
						'buttons' => [
							'view' => function ($url, $model) {
								return Yii::$app->user->can('content.page.view') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'update' => function ($url, $model) {
								return Yii::$app->user->can('content.page.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
									'title' => Yii::t('base', 'button_update'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'delete' => function ($url, $model) {
								return Yii::$app->user->can('content.page.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
									'title' => Yii::t('base', 'button_delete'),
									'data-method' => 'POST',
									'data-confirm' => Yii::t('content-page', 'confirm_delete_name', ['title' => $model->title]),
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

	<?php if (Yii::$app->user->can('content.page.create')) { ?>
	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>
	<?php } ?>

</div>
