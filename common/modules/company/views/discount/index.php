<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\base\extensions\editable\EditableColumn;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\datetimepicker\DateTimePicker;
use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\Boolean;

use common\modules\tag\models\Tag;
use common\modules\tag\helpers\enum\Type;

/* @var $this yii\web\View */
/* @var $company \common\modules\company\models\Company */
/* @var $searchModel \common\modules\company\models\search\CompanyDiscountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('company-discount', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['/company/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/company/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="company-discount-index detail-view">
	
	<?= $this->render('../default/_header', ['model' => $company]) ?>

	<div class="panel panel-default margin-20">
		<div class="panel-body">
			
			<?php Pjax::begin([
				'timeout' => 10000,
				'enablePushState' => false
			]); ?>
			
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'showFooter' => false,
				'emptyText' => Yii::t('company-discount', 'error_list_is_empty'),
				'summary' => false,
				'tableOptions' => [
					'class' => 'table table-striped'
				],
				'columns' => [
					
					// ID
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '100'],
					],
					
					// Promocode
					[
						'class' => EditableColumn::class,
						'attribute' => 'promocode',
						'url' => ['editable', 'company_id' => $company->id],
						'editableOptions' => [
							'title' => Yii::t('company-discount', 'editable_promocode'),
						],
						'pluginOptions' => [
							'disabled' => (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor),
						],
						'format' => 'raw',
					
					],
					
					// Discount
					[
						'class' => EditableColumn::class,
						'attribute' => 'discount',
						'url' => ['editable', 'company_id' => $company->id],
						'editableOptions' => [
							'title' => Yii::t('company-discount', 'editable_discount'),
						],
						'pluginOptions' => [
							'disabled' => (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor),
						],
						'headerOptions' => ['width' => '200'],
						'format' => 'raw',
					
					],

                    // Discount to
                    [
                        'class' => EditableColumn::class,
                        'attribute' => 'discount_to',
                        'url' => ['editable', 'company_id' => $company->id],
                        'editableOptions' => [
                            'title' => Yii::t('company-discount', 'editable_discount_to'),
                        ],
                        'pluginOptions' => [
                            'disabled' => (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor),
                        ],
                        'headerOptions' => ['width' => '200'],
                        'format' => 'raw',

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
							'items' => Tag::listDataType('id', 'title', 'title', [Type::NONE]),
							'clientOptions' => [
								'allowClear' => true,
								'placeholder' => '',
							],
						]),
						'format' => 'raw',
					],
					
					// Infinitely
					[
						'class' => EditableColumn::class,
						'type' => 'select2',
						'label' => Yii::t('company-discount', 'field_infinitely_short'),
						'attribute' => 'infinitely',
						'value' => function($data) {
							return Boolean::getLabel($data->infinitely);
						},
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'infinitely',
							'items' => Boolean::listData(),
							'clientOptions' => [
								'allowClear' => true,
								'hideSearch' => true,
								'placeholder' => '',
							],
						]),
						'url' => ['editable', 'company_id' => $company->id],
						'editableOptions' => function ($data) {
							return [
								'title' => Yii::t('company-discount', 'editable_infinitely'),
								'source' => Boolean::listData(),
								'hideSearch' => true,
								'value' => $data->infinitely,
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
					
					// Date start
					[
						'attribute' => 'date_start_at',
						'value' => function ($data) {
							return $data->getDate_start();
						},
						'filter' => DateTimePicker::widget([
							'model' => $searchModel,
							'attribute' => 'date_start_at',
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
					
					// Date end
					[
						'attribute' => 'date_end_at',
						'value' => function ($data) {
							return $data->getDate_end();
						},
						'filter' => DateTimePicker::widget([
							'model' => $searchModel,
							'attribute' => 'date_end_at',
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
						'url' => ['editable', 'company_id' => $company->id],
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
							'width' => '55',
							'style' => 'text-align: center;',
						],
						'template' => '{update} {delete}',
						'buttons' => [
							'update' => function ($url, $model) {
								return Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
									'update',
									'company_id' => $model->company_id,
									'id' => $model->id,
								], [
									'title' => Yii::t('base', 'button_update'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]);
							},
							'delete' => function ($url, $model) {
								return Html::a('<span class="glyphicon glyphicon-trash"></span>', [
									'delete',
									'company_id' => $model->company_id,
									'id' => $model->id,
								], [
									'title' => Yii::t('base', 'button_delete'),
									'data-method' => 'POST',
									'data-confirm' => Yii::t('company-discount', 'confirm_delete_name', ['title' => $model->title]),
									'data-pjax' => '1',
									'data-toggle' => 'tooltip',
								]);
							},
						],
					],
				],
			]); ?>
			
			<?php Pjax::end(); ?>
			
		</div>
		
	</div>
	
	<?php if (Yii::$app->user->can('company.discount.create')) { ?>
	<div class="margin-20">
		<div class="form-group margin-top-20">
			<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create', 'company_id' => $company->id], [
				'class' => 'btn btn-primary btn-lg'
			]) ?>
		</div>
	</div>
	<?php } ?>
	
</div>