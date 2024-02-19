<?php

use common\modules\content\models\Article;
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

/* @var $this yii\web\View */
/* @var $searchModel common\modules\content\models\search\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('content-article', 'title');

//$this->params['breadcrumbs'][] = ['label' => Yii::t('content', 'title'), 'url' => ['/content/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-article-index">
	
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
					
					// Id
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '100'],
					],
					
					// Title
					[
						'class' => EditableColumn::class,
						'attribute' => 'title',
						'url' => ['editable'],
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
						'label' => $searchModel->getAttributeLabel('author_id'),
						'attribute' => 'author_fio',
						'value' => function($data) {
							return $data->author->fio;
						},
						'visible' => (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor() || Yii::$app->user->getIsCompany()),
					],
					
					// Company
					[
						'label' => $searchModel->getAttributeLabel('company_id'),
						'attribute' => 'company_title',
						'value' => function($data) {
							return $data->company ? $data->company->title : '-';
						},
						'format' => 'raw',
						'visible' => (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor() || Yii::$app->user->getIsCompany()),
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
								return Yii::$app->user->can('content.article.view') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'update' => function ($url, $model) {
								$can = Yii::$app->user->can('content.article.update');
								if ($can) {
									if (!Yii::$app->user->getIsAdmin() && !Yii::$app->user->getIsEditor() && in_array($model->status, [Status::MODERATED, Status::ENABLED]))
										$can = false;
								}
								
								return $can ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
									'title' => Yii::t('base', 'button_update'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
							'delete' => function ($url, $model) {
								return Yii::$app->user->can('content.article.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
									'title' => Yii::t('base', 'button_delete'),
									'data-method' => 'POST',
									'data-confirm' => Yii::t('content-news', 'confirm_delete_name', ['title' => $model->title]),
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

	<?php if (Yii::$app->user->can('content.article.create')) { ?>
	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>
	<?php } ?>

</div>
