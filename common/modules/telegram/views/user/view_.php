<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\telegram\models\TelegramCategory;

/* @var $this yii\web\View */
/* @var $model common\modules\telegram\models\TelegramUser */

$this->title = Yii::t('telegram-user', 'title_view');

$this->params['breadcrumbs'][] = ['label' => Yii::t('telegram-user', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="telegram-user-view">
	
	<?= $this->render('_header', ['model' => $model]) ?>

	<div class="row">
		<div class="col-md-12">
			<fieldset>
				<legend><?= Yii::t('telegram-user', 'header_general') ?></legend>
				
				<?= DetailView::widget([
					'model' => $model,
					'options' => [
						'class' => 'table table-striped',
					],
					'attributes' => [
						'id',
						'username',
						'first_name',
						'last_name',
						'created_at:datetime',
						'updated_at:datetime',
					],
				]) ?>
				
			</fieldset>

			<fieldset class="margin-top-10">
				<legend><?= Yii::t('telegram-user', 'header_projects') ?></legend>
				
				<?php Pjax::begin(); ?>
				
				<?= GridView::widget([
					'dataProvider' => $dataProviderProjects,
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
				
			</fieldset>

			<fieldset class="margin-top-10">
				<legend><?= Yii::t('telegram-user', 'header_requests') ?></legend>
				
				<?php Pjax::begin(); ?>
				
				<?= GridView::widget([
					'dataProvider' => $dataProviderRequests,
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
				
			</fieldset>

			<fieldset class="margin-top-10">
				<legend><?= Yii::t('telegram-user', 'header_requests_answers') ?></legend>
				
				<?php Pjax::begin(); ?>
				
				<?= GridView::widget([
					'dataProvider' => $dataProviderRequestsAnswers,
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

			</fieldset>
			
		</div>
	</div>

	<div class="form-group margin-top-30">
		<div class="row">
			<div class="col-md-8">
				<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
						'class' => 'btn btn-default btn-lg'
				]) ?>
			</div>
			<div class="col-md-4 align-right">
			
			</div>
		</div>
	</div>

</div>
