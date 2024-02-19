<?php

use yii\helpers\Html;
use yii\grid\GridView;

use common\modules\media\widgets\show\ImageShowWidget;
use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('content-contest', 'title');

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-contest-index">

	<div class="row margin-top-20">
		<div class="col-md-12">
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'filterModel' => null,
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
						'enableSorting' => false,
					],
					
					// Id
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '70'],
						'enableSorting' => false,
					],
					
					// Author
					[
						'attribute' => 'author_fio',
						'label' => Yii::t('content', 'field_author_id'),
						'value' => function($data) {
							return $data->author->fio;
						},
						'headerOptions' => ['width' => '200'],
						'enableSorting' => false,
					],
					
					// Title
					[
						'attribute' => 'title',
						'enableSorting' => false,
					],
					
					// Title
					[
						'attribute' => 'rating',
						'label' => Yii::t('content', 'field_rating'),
						'value' => function ($data) {
							return $data->articleVotePositive;
						},
						'enableSorting' => false,
					],
					
					// Date
					[
						'attribute' => 'date_at',
						'value' => function ($data) {
							return $data->getDate();
						},
						'headerOptions' => ['width' => '170'],
						'enableSorting' => false,
					],
					
					// Actions
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '30',
							'style' => 'text-align:center;'
						],
						'template' => '{view}',
						'buttons' => [
							'view' => function ($url, $model) {
								return Yii::$app->user->can('content.article.view') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
						],
					],
				],
			]); ?>
		</div>
	</div>

</div>