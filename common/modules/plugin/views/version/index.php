<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\datetimepicker\DateTimePicker;
use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\Boolean;

/* @var $this yii\web\View */
/* @var $plugin common\modules\plugin\models\Project */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('plugin-version', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['/plugin/default/index']];
$this->params['breadcrumbs'][] = ['label' => $plugin->title, 'url' => ['/plugin/default/view', 'id' => $plugin->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="plugin-version-index detail-view">
	
	<?= $this->render('../default/_header', ['model' => $plugin]) ?>

	<div class="panel panel-default margin-20">
		<div class="panel-body">
			
			<?php Pjax::begin([
				'timeout' => 10000,
				'enablePushState' => true
			]); ?>
			
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'showFooter' => true,
                'tableOptions' => [
                    'class' => 'table table-striped'
                ],
                'columns' => [

                    // Id
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['width' => '50'],
                    ],

                    // Date
                    [
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
	                
	                // Version
	                [
	                	'attribute' => 'version',
		                'format' => 'raw',
						'headerOptions' => ['width' => '150'],
	                ],
	                
					// Text
					[
						'attribute' => 'text',
						'format' => 'raw',
					],
	
					// Status
					[
						'attribute' => 'latest',
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'latest',
							'items' => Boolean::listData(),
							'clientOptions' => [
								'allowClear' => true,
								'hideSearch' => true,
								'placeholder' => '',
							],
						]),
						'value' => function($data) {
							return Boolean::getLabel($data->latest);
						},
						'headerOptions' => ['width' => '160'],
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
                                return Yii::$app->user->can('plugin.version.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
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
                                return Yii::$app->user->can('plugin.version.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', [
                                    'delete',
                                    'plugin_id' => $plugin->id,
                                    'id' => $model->id,
                                ], [
                                    'title' => Yii::t('base', 'button_delete'),
                                    'data-method' => 'POST',
                                    'data-confirm' => Yii::t('plugin-version', 'confirm_delete_name', ['title' => $model->version]),
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

    <div class="margin-20">

        <?php if (Yii::$app->user->can('plugin.version.create')) { ?>
            <div class="form-group margin-top-20">
                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create', 'plugin_id' => $plugin->id], [
                    'class' => 'btn btn-primary btn-lg'
                ]) ?>
            </div>
        <?php } ?>

    </div>

</div>
