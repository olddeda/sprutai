<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

use kartik\grid\GridView;

use common\modules\base\extensions\dynagrid\DynaGrid;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\datetimepicker\DateTimePicker;

use common\modules\payment\helpers\enum\Status;
use common\modules\payment\models\Payment;

use common\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $plugin common\modules\plugin\models\Plugin */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('plugin-payer', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['/plugin/default/index']];
$this->params['breadcrumbs'][] = ['label' => $plugin->title, 'url' => ['/plugin/default/view', 'id' => $plugin->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="plugin-payer-index detail-view">
	
	<?= $this->render('../default/_header', ['model' => $plugin]) ?>

	<div class="panel panel-default margin-20">
		<div class="panel-body">

            <?= DynaGrid::widget([
	            'gridOptions' => [
		            'dataProvider' => $dataProvider,
		            'filterModel' => $searchModel,
		            'pjax' => false,
                    'bordered' => false,
		            'striped' => true,
		            'panelPrefix' => '',
		            'panel' => [
		                'type' => 'default',
                        'footerOptions' => [
                            'class' => '',
                        ],
                    ],
		            'toolbar' =>  [
			            [
                            'content' => '{dynagridFilter}{dynagridSort}{dynagrid}'
                        ],
			            '{toggleData}',
			            '{export}',
		            ],
		            'resizableColumns' => false,
		            'showPageSummary' => true,
	            ],
	            'storage' => DynaGrid::TYPE_COOKIE,
	            'enableMultiSort' => false,
	            'allowThemeSetting' => false,
	            'options' => ['id' => 'plugin-payer-index'],
	            'columns' => [
	             
		            // Id
		            [
		                'attribute' => 'id',
		                'headerOptions' => ['width' => '50'],
		            ],
		            
		            // Date
                    [
                        'attribute' => 'date',
                        'value' => function ($data) {
                            return $data->getDatetime();
                        },
                        'filter' => DateTimePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'date',
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
		
		            // Price
		            [
			            'attribute' => 'price',
			            'headerOptions' => ['width' => '160'],
			            'format' => 'currency',
			            'pageSummary' => true,
		            ],
		
		            // Payment type
		            [
			            'attribute' => 'payment_type_id',
			            'value' => function($data) {
				            return $data->type->title;
			            },
			            'filterType' => GridView::FILTER_SELECT2,
			            'filter' => ArrayHelper::map($plugin->paymentTypes, 'id', 'title'),
			            'filterWidgetOptions' => [
				            'pluginOptions' => [
				                'allowClear' => true,
                                'hideSearch' => true,
                            ],
			            ],
			            'filterInputOptions' => ['placeholder' => ''],
			            'vAlign' => 'middle',
			            'width' => '250px',
			            'format' => 'raw'
		            ],
		
		            // User last name
		            [
			            'attribute' => 'user_lastname',
			            'value' => function($data) {
				            return strlen($data->user->profile->last_name) ? $data->user->profile->last_name : null;
			            },
		            ],
		
		            // User first name
		            [
			            'attribute' => 'user_firstname',
			            'value' => function($data) {
				            return strlen($data->user->profile->first_name) ? $data->user->profile->first_name : null;
			            },
		            ],
		
		            // User middle name
		            [
			            'attribute' => 'user_middlename',
			            'value' => function($data) {
				            return strlen($data->user->profile->middle_name) ? $data->user->profile->middle_name : null;
			            },
		            ],
		
		            // User username
		            [
			            'attribute' => 'user_username',
			            'value' => function($data) {
				            return $data->user->username;
			            },
			            'headerOptions' => ['width' => '200'],
		            ],
		
		            // User phone
		            [
			            'attribute' => 'user_phone',
			            'value' => function($data) {
				            return ($data->user->profile->phone) ? $data->user->profile->phone : null;
			            },
		            ],
		
		
		            // User email
		            [
			            'attribute' => 'user_email',
			            'value' => function($data) {
				            return $data->user->email;
			            },
		            ],
		
		            // User telegram
		            [
			            'attribute' => 'user_telegram',
			            'value' => function($data) {
				            return $data->user->telegram ? $data->user->telegram->username : null;
			            },
		            ],
		
		            // User github
		            [
			            'attribute' => 'user_github',
			            'value' => function($data) {
				            return $data->user->github ? $data->user->github->username : null;
			            },
		            ],
		
		            // Status
		            [
						'class' => \common\modules\base\extensions\editable\EditableColumnKartik::class,
			            'attribute' => 'status',
			            'value' => function($data) {
				            return Status::getLabel($data->status);
			            },
			            'filterType' => GridView::FILTER_SELECT2,
			            'filter' => Status::listData(),
			            'filterWidgetOptions' => [
				            'pluginOptions' => [
					            'allowClear' => true,
					            'hideSearch' => true,
				            ],
			            ],
			
						'type' => 'select2',
						'url' => ['editable', 'id' => $plugin->id],
						'editableOptions' => function ($data) {
							return [
								'title' => Yii::t('plugin-payer', 'editable_status'),
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
			            
			            'filterInputOptions' => ['placeholder' => ''],
			            'vAlign' => 'middle',
			            'width' => '160px',
		            ],
		
		            // Actions
		            [
			            'class' => 'kartik\grid\ActionColumn',
                        'order' => DynaGrid::ORDER_FIX_RIGHT,
			            'template' => '{update} {delete}',
			            'headerOptions' => [
				            'width' => '55',
				            'style' => 'text-align:center;'
			            ],
			            'buttons' => [
				            'update' => function ($url, $model) use ($plugin) {
					            return Yii::$app->user->can('plugin.payer.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
						            'update',
						            'plugin_id' => $plugin->id,
						            'id' => $model->id,
					            ], [
						            'title' => Yii::t('base', 'button_update'),
						            'data-pjax' => '0',
						            'data-toggle' => 'tooltip',
					            ]) : '';
				            },
				            'delete' => function ($url, $model) use ($plugin) {
					            return Yii::$app->user->can('plugin.payer.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', [
						            'delete',
						            'plugin_id' => $plugin->id,
						            'id' => $model->id,
					            ], [
						            'title' => Yii::t('base', 'button_delete'),
						            'data-method' => 'POST',
						            'data-confirm' => Yii::t('plugin-payer', 'confirm_delete_name', ['title' => $model->title]),
						            'data-pjax' => '1',
						            'data-toggle' => 'tooltip',
					            ]) : '';
				            },
			            ],
		            ],
                ],
            ]); ?>

            <? /*= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'showFooter' => true,
                'tableOptions' => [
                    'class' => 'table table-striped'
                ],
                'columns' => [

                    // Id
                    //[
                    //    'attribute' => 'id',
                    //    'headerOptions' => ['width' => '50'],
                    //],

                    // Date
                    /*[
                        'attribute' => 'date_at',
                        'value' => function ($data) {
                            return $data->getDatetime();
                        },
                        'filter' => DateTimePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'date_at',
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
 
    
                    // Price
                    [
                        'attribute' => 'price',
                        'headerOptions' => ['width' => '160'],
                        'format' => 'currency',
	                    'footer' => Yii::t('plugin-payer', 'footer_total_price', ['sum' => Yii::$app->formatter->asCurrency(Payment::totalSum($dataProvider->models, 'price'))]),
                    ],
    
                    // Payment type
                    [
                        'attribute' => 'payment_type_id',
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'attribute' => 'payment_type_id',
                            'items' => ArrayHelper::map($plugin->paymentTypes, 'id', 'title'),
                            'clientOptions' => [
                                'allowClear' => true,
                                'hideSearch' => true,
                                'placeholder' => '',
                            ],
                        ]),
                        'value' => function($data) {
                            return $data->type->title;
                        },
                        'headerOptions' => ['width' => '300'],
                    ],
	
	                // User last name
	                [
		                'attribute' => 'user_lastname',
		                'value' => function($data) {
			                return strlen($data->user->profile->last_name) ? $data->user->profile->last_name : null;
		                },
	                ],

                    // User first name
                    [
                        'attribute' => 'user_firstname',
                        'value' => function($data) {
	                        return strlen($data->user->profile->first_name) ? $data->user->profile->first_name : null;
                        },
                    ],
	
	                // User middle name
	                [
		                'attribute' => 'user_middlename',
		                'value' => function($data) {
			                return strlen($data->user->profile->middle_name) ? $data->user->profile->middle_name : null;
		                },
	                ],
	
	                // User username
	                [
		                'attribute' => 'user_username',
		                'value' => function($data) {
			                return $data->user->username;
		                },
		                'headerOptions' => ['width' => '200'],
	                ],
                    
                    
                    // User email
                    [
                        'attribute' => 'user_email',
                        'value' => function($data) {
                            return $data->user->email;
                        },
                        'headerOptions' => ['width' => '200'],
                    ],
	
	                // User address
	                [
		                'attribute' => 'user_address',
		                'value' => function($data) {
			                return $data->user->address ? $data->user->address->address : null;
		                },
	                ],

                    // Status
                    [
                        'attribute' => 'status',
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
                        'value' => function($data) {
                            return Status::getLabel($data->status);
                        },
                        'headerOptions' => ['width' => '160'],
                    ],
                    
                    // Actions
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'headerOptions' => [
                            'width' => '55',
                            'style' => 'text-align:center;'
                        ],
                        'buttons' => [
                            'update' => function ($url, $model) use ($plugin) {
                                return Yii::$app->user->can('plugin.payer.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
                                    'update',
                                    'plugin_id' => $plugin->id,
                                    'id' => $model->id,
                                ], [
                                    'title' => Yii::t('base', 'button_update'),
                                    'data-pjax' => '0',
                                    'data-toggle' => 'tooltip',
                                ]) : '';
                            },
                            'delete' => function ($url, $model) use ($plugin) {
                                return Yii::$app->user->can('plugin.payer.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', [
                                    'delete',
                                    'plugin_id' => $plugin->id,
                                    'id' => $model->id,
                                ], [
                                    'title' => Yii::t('base', 'button_delete'),
                                    'data-method' => 'POST',
                                    'data-confirm' => Yii::t('plugin-payer', 'confirm_delete_name', ['title' => $model->title]),
                                    'data-pjax' => '1',
                                    'data-toggle' => 'tooltip',
                                ]) : '';
                            },
                        ],
                    ],
                ],
            ]); */ ?>
            <?php //Pjax::end(); ?>

		</div>
	</div>

    <div class="margin-20">

        <?php if (Yii::$app->user->can('plugin.payer.create')) { ?>
            <div class="form-group margin-top-20">
                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create', 'plugin_id' => $plugin->id], [
                    'class' => 'btn btn-primary btn-lg'
                ]) ?>
            </div>
        <?php } ?>

    </div>

</div>
