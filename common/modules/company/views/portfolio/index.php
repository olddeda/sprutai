<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\base\extensions\editable\EditableColumn;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\datetimepicker\DateTimePicker;

use common\modules\media\widgets\show\ImageShowWidget;
use common\modules\media\helpers\enum\Mode;

use common\modules\tag\models\Tag;
use common\modules\tag\helpers\enum\Type;

use common\modules\content\helpers\enum\Status;
use common\modules\content\models\Portfolio;

/* @var $this yii\web\View */
/* @var $company \common\modules\company\models\Company */
/* @var $searchModel \common\modules\content\models\search\PortfolioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('content-portfolio', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['/company/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/company/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="company-portfolio-index detail-view">
	
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
					
					// Id
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '100'],
					],
					
					// Title
					[
						'class' => EditableColumn::class,
						'attribute' => 'title',
						'url' => ['editable', 'company_id' => $company->id],
						'editableOptions' => [
							'title' => Yii::t('content', 'editable_title'),
						],
						'pluginOptions' => [
							'disabled' => (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor),
						],
						'format' => 'raw',
					
					],
					
					// Author
					[
						'class' => EditableColumn::class,
						'label' => $searchModel->getAttributeLabel('author_id'),
						'attribute' => 'author_fio',
						'value' => function($data) {
							return $data->author->fio;
						},
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'author_id',
							'items' => Portfolio::users(),
							'clientOptions' => [
								'allowClear' => true,
								'placeholder' => '',
							],
						]),
						'type' => 'select2',
						'url' => ['editable', 'company_id' => $company->id],
						'editableOptions' => function ($data) {
							return [
								'title' => Yii::t('content', 'editable_user_id'),
								'source' => Portfolio::users(),
								'name' => 'author_id',
								'value' => $data->author_id,
							];
						},
						'pluginOptions' => [
							'select2' => [
								'width' => 250,
							],
						],
						'headerOptions' => ['width' => '200'],
						'visible' => (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor()),
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
						'headerOptions' => ['width' => '300'],
						'format' => 'raw',
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
					
					// Date
					[
						'attribute' => 'date_at',
						'value' => function ($data) {
							return $data->getDate();
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
					
					// Actions
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '70',
							'style' => 'text-align:center;'
						],
						'buttons' => [
							'view' => function ($url, $model) {
								return Yii::$app->user->can('company.portfolio.view') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', [
									'view',
									'company_id' => $model->company_id,
									'id' => $model->id,
								], [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'update' => function ($url, $model) use ($company) {
								$can = Yii::$app->user->can('company.portfolio.update');
								if ($can) {
									if (!Yii::$app->user->getIsAdmin() && !Yii::$app->user->getIsEditor() && in_array($model->status, [Status::MODERATED, Status::ENABLED]))
										$can = false;
									if (in_array($model->author_id, $company->getUsersIds()))
										$can = true;
								}
								
								return $can ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
									'update',
									'company_id' => $model->company_id,
									'id' => $model->id,
								], [
									'title' => Yii::t('base', 'button_update'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'delete' => function ($url, $model) {
								return Yii::$app->user->can('company.portfolio.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', [
									'delete',
									'company_id' => $model->company_id,
									'id' => $model->id,
								], [
									'title' => Yii::t('base', 'button_delete'),
									'data-method' => 'POST',
									'data-confirm' => Yii::t('content-portfolio', 'confirm_delete_name', ['title' => $model->title]),
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
	
	<?php if (Yii::$app->user->can('company.portfolio.create')) { ?>
	<div class="margin-20">
		<div class="form-group margin-top-20">
			<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create', 'company_id' => $company->id], [
				'class' => 'btn btn-primary btn-lg'
			]) ?>
		</div>
	</div>
	<?php } ?>
	
</div>
