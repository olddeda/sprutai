<?php

use common\modules\tag\helpers\enum\Type;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\base\extensions\editable\EditableColumn;
use common\modules\base\extensions\select2\Select2;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\media\widgets\show\ImageShowWidget;
use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\tag\models\search\TagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('tag', 'title');

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tag-index">
	
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
					
					// Image
					[
						'attribute' => 'media_image',
						'format' => 'html',
						'label' => '',
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
					
					// ID
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '60'],
					],

                    // Type
                    [
                        'attribute' => 'type',
                        'value' => function($data) {
                            return $data->getTypesName('<br>');
                        },
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'attribute' => 'type',
                            'items' => Type::listData(),
                            'clientOptions' => [
                                'allowClear' => true,
                                'hideSearch' => true,
                                'placeholder' => '',
                            ],
                        ]),
                        'headerOptions' => ['width' => '180'],
                        'format' => 'raw',
                    ],
					
					// Title
					[
						'class' => EditableColumn::className(),
						'attribute' => 'title',
						'url' => ['editable'],
						'editableOptions' => [
							'title' => Yii::t('tag', 'editable_title'),
						],
					],
					
					
					// Status
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
								'title' => Yii::t('tag', 'editable_status'),
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
							'width' => '50',
							'style' => 'text-align:center;'
						],
						'template' => '{update} {delete}',
						'buttons' => [
							'view' => function ($url, $model) {
								return Yii::$app->user->can('tag.default.view') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'update' => function ($url, $model) {
								return Yii::$app->user->can('tag.default.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
									'title' => Yii::t('base', 'button_update'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'delete' => function ($url, $model) {
								return Yii::$app->user->can('tag.default.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
									'title' => Yii::t('base', 'button_delete'),
									'data-method' => 'POST',
									'data-confirm' => Yii::t('tag', 'confirm_delete_name', ['title' => $model->title]),
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

	<?php if (Yii::$app->user->can('tag.default.create')) { ?>
	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>
	<?php } ?>

</div>
