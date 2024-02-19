<?php

use yii\helpers\Html;

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $company \common\modules\company\models\Company */
/* @var $searchModel \common\modules\company\models\search\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('company-address', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['/company/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/company/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="company-address-index detail-view">
	
	<?= $this->render('../default/_header', ['model' => $company]) ?>

	<div class="panel panel-default margin-20">
		<div class="panel-body">
			
			<?php Pjax::begin([
				'timeout' => 10000,
				'enablePushState' => false
			]); ?>
			
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'showHeader' => $dataProvider->count,
				'showFooter' => false,
				'emptyText' => Yii::t('company-address', 'error_list_is_empty'),
				'summary' => false,
				'tableOptions' => [
					'class' => 'table table-striped'
				],
				'columns' => [
					
					[
						'attribute' => 'address',
					],
					
					[
						'attribute' => 'is_primary',
						'format' => 'boolean',
						'headerOptions' => ['width' => '150'],
					],
					
					// Actions
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '70',
							'style' => 'text-align: center;',
						],
						'template' => '{primary} {update} {delete}',
						'buttons' => [
							'primary' => function ($url, $model) {
								return ($model->is_primary) ? '<span class="glyphicon glyphicon-heart"></span>' : Html::a('<span class="glyphicon glyphicon-heart-empty"></span>', [
									'primary',
									'company_id' => $model->company_id,
									'id' => $model->id,
								], [
									'title' => Yii::t('user-address', 'button_primary'),
									'data-method' => 'POST',
									'data-confirm' => Yii::t('user-address', 'confirm_primary_name', ['title' => $model->address]),
									'data-pjax' => '1',
									'data-toggle' => 'tooltip',
								]);
							},
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
									'data-confirm' => Yii::t('user-address', 'confirm_delete_name', ['title' => $model->address]),
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
	
	<?php if (Yii::$app->user->can('company.address.create')) { ?>
	<div class="margin-20">
		<div class="form-group margin-top-20">
			<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create', 'company_id' => $company->id], [
				'class' => 'btn btn-primary btn-lg'
			]) ?>
		</div>
	</div>
	<?php } ?>
	
</div>