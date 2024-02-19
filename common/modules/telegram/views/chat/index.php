<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\datetimepicker\DateTimePicker;
use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\Boolean;
use common\modules\base\extensions\editable\EditableColumn;

use common\modules\media\widgets\show\ImageShowWidget;
use common\modules\media\helpers\enum\Mode;

use common\modules\tag\models\Tag;
use common\modules\tag\helpers\enum\Type as TagType;

/* @var $this yii\web\View */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('telegram-chat', 'title');

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="telegram-chat-index detail-view">

	<div class="panel panel-default margin-20">
		<div class="panel-body">
			
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
	
					// Image
					[
						'attribute' => 'logo',
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

                    // Id
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['width' => '50'],
                    ],
	
					// Title
					[
						'class' => EditableColumn::class,
						'attribute' => 'title',
						'url' => ['editable'],
						'editableOptions' => [
							'title' => Yii::t('telegram-chat', 'editable_title'),
						],
						'pluginOptions' => [
							'disabled' => (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor),
						],
						'format' => 'raw',
					],
	
					// Identifier
					[
						'class' => EditableColumn::class,
						'attribute' => 'identifier',
						'url' => ['editable'],
						'editableOptions' => [
							'title' => Yii::t('telegram-chat', 'editable_identifier'),
						],
						'pluginOptions' => [
							'disabled' => (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor),
						],
						'format' => 'raw',
						'headerOptions' => ['width' => '200'],
					],
	
					// Username
					[
						'class' => EditableColumn::class,
						'attribute' => 'username',
						'url' => ['editable'],
						'editableOptions' => [
							'title' => Yii::t('telegram-chat', 'editable_username'),
						],
						'pluginOptions' => [
							'disabled' => (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor),
						],
						'format' => 'raw',
						'headerOptions' => ['width' => '250'],
					],
	
					// Tags ids
					[
						'attribute' => 'tags_ids',
						'value' => function($data) {
							return $data->getTagsValues(false, '<br/>');
						},
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'tags_ids',
							'items' => Tag::listDataType('id', 'title', 'title', [TagType::SYSTEM]),
							'clientOptions' => [
								'allowClear' => true,
								'placeholder' => '',
							],
						]),
						'headerOptions' => ['width' => '300'],
						'format' => 'raw',
					],

                    // Is partner
                    [
                        'attribute' => 'is_partner',
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'attribute' => 'is_partner',
                            'items' => Boolean::listData(),
                            'clientOptions' => [
                                'allowClear' => true,
                                'hideSearch' => true,
                                'placeholder' => '',
                            ],
                        ]),
                        'value' => function($data) {
                            return Boolean::getLabel($data->is_partner);
                        },
                        'headerOptions' => ['width' => '100'],
                    ],
	
					// Notify Content
					[
						'attribute' => 'notify_content',
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'notify_content',
							'items' => Boolean::listData(),
							'clientOptions' => [
								'allowClear' => true,
								'hideSearch' => true,
								'placeholder' => '',
							],
						]),
						'value' => function($data) {
							return Boolean::getLabel($data->notify_content);
						},
						'headerOptions' => ['width' => '100'],
					],
	
					// Notify Payment
					[
						'attribute' => 'notify_payment',
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'notify_payment',
							'items' => Boolean::listData(),
							'clientOptions' => [
								'allowClear' => true,
								'hideSearch' => true,
								'placeholder' => '',
							],
						]),
						'value' => function($data) {
							return Boolean::getLabel($data->notify_payment);
						},
						'headerOptions' => ['width' => '100'],
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
                            'update' => function ($url, $model) {
                                return Yii::$app->user->can('telegram.chat.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
                                    'update',
                                    'id' => $model->id,
                                ], [
                                    'title' => Yii::t('base', 'button_update'),
                                    'data-pjax' => '0',
                                    'data-toggle' => 'tooltip',
                                ]) : '';
                            },
                            'delete' => function ($url, $model) {
                                return Yii::$app->user->can('telegram.chat.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', [
                                    'delete',
                                    'id' => $model->id,
                                ], [
                                    'title' => Yii::t('base', 'button_delete'),
                                    'data-method' => 'POST',
                                    'data-confirm' => Yii::t('telegram-chat', 'confirm_delete_name', ['title' => $model->title]),
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

        <?php if (Yii::$app->user->can('telegram.chat.create')) { ?>
            <div class="form-group margin-top-20">
                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
                    'class' => 'btn btn-primary btn-lg'
                ]) ?>
            </div>
        <?php } ?>

    </div>

</div>
