<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\base\helpers\enum\Status;
use common\modules\base\extensions\editable\EditableColumn;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\shortener\models\search\ShortenerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('shortener', 'title');

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="shortener-index">
	
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
						'headerOptions' => ['width' => '100'],
					],
                    
                    // Url
                    [
                        'attribute' => 'title',
                        'format' => 'raw',
                    ],
					
					// Url
					[
						'attribute' => 'url',
						'value' => function($data) {
							return Html::a($data->url, $data->url, ['target' => '_blank', 'data-pjax' => 0]);
						},
						'format' => 'raw',
					],
                    
                    // Short url
                    [
                        'attribute' => 'shorturl',
	                    'label' => Yii::t('shortener', 'field_short_url'),
                        'value' => function($data) {
                            return Html::a($data->short_url, $data->short_url, ['target' => '_blank', 'data-pjax' => 0]);
                        },
                        'format' => 'raw',
                    ],
                    
                    // Counter
                    [
                        'attribute' => 'counter',
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
                                'title' => Yii::t('shortener', 'editable_status'),
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
					
					// Expiration date
					[
						'attribute' => 'expiration_at',
						'value' => function ($data) {
							return $data->getExpiration_date();
						},
						'filter' => DateTimePicker::widget([
							'model' => $searchModel,
							'attribute' => 'expiration_at',
							'template' => '{input}{button}{reset}',
							'language' => 'ru',
							'pickButtonIcon' => 'glyphicon glyphicon-calendar',
							'clientOptions' => [
								'autoclose' => true,
								'format' => 'dd-mm-yyyy',
								'todayBtn' => true,
								'minView' => 2,
							],
						]),
						'headerOptions' => ['width' => '170'],
					],
					
					// Created date
					[
						'attribute' => 'created_at',
						'value' => function ($data) {
							return $data->getCreated_date();
						},
						'filter' => DateTimePicker::widget([
							'model' => $searchModel,
							'attribute' => 'created_at',
							'template' => '{input}{button}{reset}',
							'language' => 'ru',
							'pickButtonIcon' => 'glyphicon glyphicon-calendar',
							'clientOptions' => [
								'autoclose' => true,
								'format' => 'dd-mm-yyyy',
								'todayBtn' => true,
								'minView' => 2,
							],
						]),
						'headerOptions' => ['width' => '170'],
					],
					
					// Actions
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '70',
							'style' => 'text-align:center;'
						],
						'template' => '{view} {update} {delete}',
						'buttons' => [
							'view' => function ($url, $model) {
								return Yii::$app->user->can('shortener.default.view') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'update' => function ($url, $model) {
								return Yii::$app->user->can('shortener.default.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
									'title' => Yii::t('base', 'button_update'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'delete' => function ($url, $model) {
								return Yii::$app->user->can('shortener.default.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
									'title' => Yii::t('base', 'button_delete'),
									'data-method' => 'POST',
									'data-confirm' => Yii::t('shortener', 'confirm_delete_name', ['title' => $model->title]),
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

	<?php if (Yii::$app->user->can('shortener.default.create')) { ?>
	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>
	<?php } ?>

</div>
