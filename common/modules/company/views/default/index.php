<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;


use common\modules\base\helpers\enum\Status;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\editable\EditableColumn;

use common\modules\media\widgets\show\ImageShowWidget;
use common\modules\media\helpers\enum\Mode;

use common\modules\company\helpers\enum\Type;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\company\models\search\Company */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('company', 'title');

$this->params['breadcrumbs'][] = Yii::t('company', 'title');

?>

<div class="lookup-index">
	
	<?php Pjax::begin([
		'timeout' => 7000,
		'enablePushState' => false,
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
				'format' => 'raw',
				'attribute' => 'title',
			],
	
			// Tag
			[
				'format' => 'raw',
				'label' => $searchModel->getAttributeLabel('tag_id'),
				'attribute' => 'tag_title',
				'value' => function ($data) {
    	            return ($data->tag) ? $data->tag->title : '-';
				},
			],
	
			// Site
			[
				'format' => 'raw',
				'attribute' => 'site',
				'value' => function ($data) {
    	            return Html::a($data->site, $data->site, ['target' => '_blank']);
				}
			],
	
			// Email
			[
				'format' => 'raw',
				'attribute' => 'email',
				'value' => function ($data) {
					return Html::mailto($data->email, $data->email);
				}
			],
	
			// Phone
			[
				'format' => 'raw',
				'attribute' => 'phone',
				'value' => function ($data) {
					return $data->phone;
				}
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
						'title' => Yii::t('content', 'editable_status'),
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
					'disabled' => (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor),
				],
				'headerOptions' => ['width' => '150'],
			],
	        
	        // Actions
			[
				'class' => 'yii\grid\ActionColumn',
				'headerOptions' => [
					'width' => '70',
					'style' => 'text-align:center;'
				],
				'buttons' => [
					'view' => function ($url, $model) {
						return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
							'title' => Yii::t('base', 'button_view'),
							'data-pjax' => '0',
							'data-toggle' => 'tooltip',
						]);
					},
					'update' => function ($url, $model) {
						return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
							'title' => Yii::t('base', 'button_update'),
							'data-pjax' => '0',
							'data-toggle' => 'tooltip',
						]);
					},
					'delete' => function ($url, $model) {
						return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
							'title' => Yii::t('base', 'button_delete'),
							'data-method' => 'POST',
							'data-confirm' => Yii::t('company', 'confirm_delete_name', ['title' => $model->title]),
							'data-pjax' => '1',
							'data-toggle' => 'tooltip',
						]);
					},
				],
			],
        ],
    ]); ?>
	<?php Pjax::end(); ?>
	
	<?php if (Yii::$app->user->can('company.default.create')) { ?>
		<div class="form-group margin-top-20 margin-bottom-0">
			<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
				'class' => 'btn btn-primary btn-lg'
			]) ?>
		</div>
	<?php } ?>

</div>
