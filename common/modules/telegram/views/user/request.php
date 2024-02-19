<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\modules\telegram\models\TelegramUser */

$this->title = Yii::t('telegram-user', 'title_view_requests');

$this->params['breadcrumbs'][] = ['label' => Yii::t('telegram-user', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getFullname(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="telegram-user-view detail-view">
	
	<?= $this->render('_header', ['model' => $model]) ?>
	
	<div class="panel panel-default margin-20">
		
		<div class="panel-body">
			
			<?php Pjax::begin(); ?>
			
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'tableOptions' => [
					'class' => 'table table-striped'
				],
				'summary' => '',
				'columns' => [
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '70'],
					],
					
					[
						'attribute' => 'categories',
						'value' => function ($data) {
							$tmp = [];
							foreach ($data->categories as $row) {
								$tmp[] = Html::a($row->title, ['category/view', 'id' => $row->id], ['data-pjax' => '0']);
							}
							return implode('<br>', $tmp);
						},
						'format' => 'raw',
						'headerOptions' => ['width' => '250'],
					],
					
					'text:ntext',
					
					[
						'filter' => false,
						'attribute' => 'created_at',
						'format' => 'datetime',
						'headerOptions' => ['width' => '150'],
					],
					
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '30',
							'style' => 'text-align:center;'
						],
						'template' => '{view}',
						'buttons' => [
							'view' => function ($url, $data) {
								return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['request/view', 'id' => $data->id], [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
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

</div>