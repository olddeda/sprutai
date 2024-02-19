<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\telegram\models\TelegramCategory;

/* @var $this yii\web\View */
/* @var $model common\modules\telegram\models\TelegramUser */

$this->title = Yii::t('telegram-user', 'title_view_projects');

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
						'filter' => TelegramCategory::listData(),
						'attribute' => 'category_id',
						'value' => function ($data) {
							return Html::a($data->category->title, ['category/view', 'id' => $data->category->id], ['data-pjax' => '0']);
						},
						'format' => 'raw',
					],
					
					'title',
					'phone',
					'email:email',
					
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
								return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['project/view', 'id' => $data->id], [
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