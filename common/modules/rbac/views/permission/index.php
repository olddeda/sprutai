<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var $dataProvider array
 * @var $this yii\web\View
 * @var $filterModel common\modules\rbac\models\Search
 */

$this->title = Yii::t('rbac-permission', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'title'), 'url' => ['/rbac/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="permission-index">
	<div class="row margin-top-20">
		<div class="col-md-12">
			<?php Pjax::begin([
				'timeout' => 10000,
				'enablePushState' => false
			]); ?>
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'filterModel' => $filterModel,
				'layout' => "{items}\n{pager}",
				'tableOptions' => [
					'class' => 'table table-striped'
				],
				'columns' => [
					[
						'attribute' => 'name',
						'label' => Yii::t('rbac', 'field_name'),
						'value' => function($data) {
							return Html::a($data['name'], [
								'/rbac/permission/view',
								'name' => $data['name']
							], [
								'title' => Yii::t('rbac-permission', 'tooltip_name'),
								'data-pjax' => '0',
								'data-toggle' => 'tooltip',
							]);
						},
						'options' => [
							'style' => 'width: 20%'
						],
						'format' => 'raw',
					],
					[
						'attribute' => 'description',
						'label' => Yii::t('rbac', 'field_description'),
						'value' => function($data) {
							return Html::a($data['description'], [
								'/rbac/permission/view',
								'name' => $data['name']
							], [
								'title' => Yii::t('rbac-permission', 'tooltip_name'),
								'data-pjax' => '0',
								'data-toggle' => 'tooltip',
							]);
						},
						'options' => [
							'style' => 'width: 54%'
						],
						'format' => 'raw',
					],
					[
						'attribute' => 'rule_name',
						'label' => Yii::t('rbac', 'field_rule'),
						'options' => [
							'style' => 'width: 20%'
						],
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'template' => '{view} {update} {delete}',
						'buttons' => [
							'view' => function ($url, $data) {
								return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', [
									'/rbac/permission/view',
									'name' => $data['name'],
								], [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]);
							},
							'update' => function ($url, $data) {
								return Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
									'/rbac/permission/update',
									'name' => $data['name'],
								], [
									'title' => Yii::t('base', 'button_update'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]);
							},
							'delete' => function ($url, $data) {
								return Html::a('<span class="glyphicon glyphicon-trash"></span>', [
									'/rbac/permission/delete',
									'name' => $data['name'],
								], [
									'title' => Yii::t('base', 'button_delete'),
									'data-method' => 'POST',
									'data-confirm' => Yii::t('rbac-permission', 'confirm_delete_name', ['title' => $data['name']]),
									'data-pjax' => '1',
									'data-toggle' => 'tooltip',
								]);
							},
						],
						'options' => [
							'style' => 'text-align:center; width: 6%'
						],
					],
				],
			]) ?>
			<?php Pjax::end(); ?>
		</div>
	</div>
	
	<?php if (Yii::$app->user->can('rbac.permission.create')) { ?>
	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>
	<?php } ?>
	
</div>