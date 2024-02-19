<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\helpers\enum\Status;

use common\modules\media\helpers\enum\Mode;
use common\modules\media\widgets\show\ImageShowWidget;

/* @var $this yii\web\View */
/* @var $company common\modules\company\models\Company */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('company-user', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['/company/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/company/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="company-user-index detail-view">
	
	<?= $this->render('../default/_header', ['model' => $company]) ?>

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
                'emptyText' => Yii::t('company-user', 'list_is_empty'),
                'columns' => [
	
					// Image
					[
						'attribute' => 'logo',
						'format' => 'html',
						'label' => '',
						'headerOptions' => ['width' => '60'],
						'value' => function ($data) {
							return ImageShowWidget::widget([
								'model' => $data->user,
								'width' => 50,
								'height' => 50,
								'mode' => Mode::CROP_CENTER,
							]);
						},
					],
	
					// User fio
					[
						'attribute' => 'user_fio',
						'value' => function($data) {
							return $data->user->getFio();
						},
					],
                		
                    // User email
                    [
                        'attribute' => 'user_email',
                        'value' => function($data) {
                            return $data->user->email;
                        },
                    ],
	
					// User phone
					[
						'attribute' => 'user_phone',
						'value' => function($data) {
							return $data->user->profile->phone;
						},
					],
	
					// User telegram
					[
						'attribute' => 'user_telegram',
						'value' => function($data) {
							return $data->user->telegram && $data->user->telegram->username ? $data->user->telegram->username : '-';
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
                        'template' => '{delete}',
                        'headerOptions' => [
                            'width' => '55',
                            'style' => 'text-align:center;'
                        ],
                        'buttons' => [
                            'update' => function ($url, $model) use ($company) {
                                return Yii::$app->user->can('company.user.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
                                    'update',
                                    'company_id' => $company->id,
                                    'id' => $model->id,
                                ], [
                                    'title' => Yii::t('base', 'button_update'),
                                    'data-pjax' => '0',
                                    'data-toggle' => 'tooltip',
                                ]) : '';
                            },
                            'delete' => function ($url, $model) use ($company) {
                                return Yii::$app->user->can('company.user.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', [
                                    'delete',
                                    'company_id' => $company->id,
                                    'id' => $model->id,
                                ], [
                                    'title' => Yii::t('base', 'button_delete'),
                                    'data-method' => 'POST',
                                    'data-confirm' => Yii::t('company-user', 'confirm_delete_name', ['title' => $model->user->getFio()]),
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
	
	<?php if (Yii::$app->user->can('company.user.create')) { ?>
    <div class="margin-20">
        <div class="form-group margin-top-20">
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create', 'company_id' => $company->id], [
                'class' => 'btn btn-primary btn-lg'
            ]) ?>
        </div>
    </div>
	<?php } ?>

</div>
