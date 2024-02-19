<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\base\helpers\Url;
use common\modules\base\helpers\enum\Status;

use common\modules\lookup\models\Lookup;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\lookup\models\search\LookupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lookup', 'title_index');

if ($this->context->parentModel) {
	$this->title .= ' - '.$this->context->parentModel->title;
	
	$this->params['breadcrumbs'][] = ['label' => Yii::t('lookup', 'title_index'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->context->parentModel->title;
}
else {
	$this->params['breadcrumbs'][] = Yii::t('lookup', 'title_index');
}

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
            [
				'class' => 'yii\grid\SerialColumn',
				'headerOptions' => ['width' => '80'],
			],
			[
				'filter' => Lookup::types(),
				'attribute' => 'type',
				'value' => function ($data) {
					return Lookup::types($data->type);
				},
				'headerOptions' => ['width' => '160'],
			],
            [
				'format' => 'raw',
				'attribute' => 'title',
			],
			[
				'attribute' => 'sequence',
				'headerOptions' => ['width' => '100'],
			],
			[
				'filter' => Status::listData(),
				'attribute' => 'status',
				'value' => function($data) {
					return Status::getLabel($data->status);
				},
				'headerOptions' => ['width' => '120'],
			],
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
							'data-confirm' => Yii::t('lookup', 'confirm_delete_name', ['title' => $model->title]),
							'data-pjax' => '1',
							'data-toggle' => 'tooltip',
						]);
					},
				],
			],
        ],
    ]); ?>
	<?php Pjax::end(); ?>

	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), Url::toRouteParams(['create'], $this->context->parentParams), [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>

</div>
