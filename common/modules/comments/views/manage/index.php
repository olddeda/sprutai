<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\widgets\Pjax;

use common\modules\base\extensions\datetimepicker\DateTimePicker;
use common\modules\base\extensions\editable\EditableColumn;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\helpers\enum\ModuleType;


use common\modules\comments\helpers\enum\Status;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $commentModel \common\modules\comments\models\Comment */
/* @var $searchModel \common\modules\comments\models\search\CommentSearch */

$this->title = Yii::t('comments', 'title');

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="comments-index">
	
    <?php Pjax::begin([
		'enablePushState' => false,
		'timeout' => 5000
	]); ?>
    <?php echo GridView::widget([
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
			
			// ModuleType
			[
				'attribute' => 'module_type',
				'value' => function($data) {
					return ModuleType::getLabel($data->module_type);
				},
				'filter' => Select2::widget([
					'model' => $searchModel,
					'attribute' => 'module_type',
					'items' => [ModuleType::CONTENT_ARTICLE => ModuleType::getLabel(ModuleType::CONTENT_ARTICLE)],
					'clientOptions' => [
						'allowClear' => true,
						'hideSearch' => true,
						'placeholder' => '',
					],
				]),
				'headerOptions' => ['width' => '150'],
			],
			[
				'format' => 'raw',
				'attribute' => 'related_to',
			],
            [
            	'format' => 'raw',
                'attribute' => 'content',
                'value' => function ($model) {
                    return StringHelper::truncate($model->content, 100);
                }
            ],
            [
				'label' => Yii::t('comments', 'field_author'),
                'attribute' => 'author_search',
                'value' => function ($model) {
                    return $model->getAuthorName();
                },
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
						'title' => Yii::t('comments', 'editable_status'),
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
			[
				'attribute' => 'created_at',
				'format' => ['datetime', 'php:d-m-Y H:i'],
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
			[
				'class' => 'yii\grid\ActionColumn',
				'headerOptions' => [
					'width' => '50',
					'style' => 'text-align:center;'
				],
				'template' => '{update} {delete}',
				'buttons' => [
					'update' => function ($url, $model) {
						return Yii::$app->user->can('comments.manage.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
							'title' => Yii::t('base', 'button_update'),
							'data-pjax' => '0',
							'data-toggle' => 'tooltip',
						]) : '';
					},
					'delete' => function ($url, $model) {
						return Yii::$app->user->can('comments.manage.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
							'title' => Yii::t('base', 'button_delete'),
							'data-method' => 'POST',
							'data-confirm' => Yii::t('comments', 'confirm_delete_name', ['title' => StringHelper::truncate($model->content, 100)]),
							'data-pjax' => '1',
							'data-toggle' => 'tooltip',
						]) : '';
					},
				],
			],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
