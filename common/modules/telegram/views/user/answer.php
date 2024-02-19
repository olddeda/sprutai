<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\modules\telegram\models\TelegramUser */

$this->title = Yii::t('telegram-user', 'title_view_answers');

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
						'attribute' => 'telegram_request_id',
						'value' => function ($data) {
							return Html::a($data->request->number, ['request/view', 'id' => $data->request->id], ['data-pjax' => '0']);
						},
						'format' => 'raw',
						'headerOptions' => ['width' => '100'],
					],
					/*
					[
						'attribute' => 'telegram_project_id',
						'value' => function ($data) {
							return Html::a($data->project->title, ['project/view', 'id' => $data->project->id], ['data-pjax' => '0']);
						},
						'format' => 'raw',
					],
					*/
					'text:ntext',
					
					[
						'filter' => false,
						'attribute' => 'created_at',
						'format' => 'datetime',
						'headerOptions' => ['width' => '150'],
					],
				],
			]); ?>
			
			<?php Pjax::end(); ?>
			
		</div>
	</div>

</div>