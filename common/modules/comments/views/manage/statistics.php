<?php

use yii\helpers\Html;

use dosamigos\chartjs\ChartJs;

use common\modules\comments\models\Comment;

$this->title = Yii::t('comments', 'statistics_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('comments', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('canvas {width: 100% !important;height: 400px;}');
?>
<div class="media-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="row">
		<div class="col-md-12 col-lg-12">
			<div class="well">
				<?php
				$days = [];
				$count = [];
				foreach (range(-6, 0) as $day) {
					$date = strtotime($day . 'days');
					$days[] = Yii::$app->formatter->asDate($date, 'php:d M Y'); //date('D: Y-m-d', $date);
					$count[] = Comment::find()->andWhere(['between', 'created_at', strtotime(date('Y-m-d 00:00:00', $date)), strtotime(date('Y-m-d 23:59:59', $date))])->count();
				}
				echo ChartJs::widget([
					'type' => 'Bar',
					'data' => [
						'labels' => $days,
						'datasets' => [
							[
								'fillColor' => 'rgba(151,187,205,0.5)',
								'strokeColor' => 'rgba(151,187,205,1)',
								'pointColor' => 'rgba(151,187,205,1)',
								'pointStrokeColor' => '#fff',
								'data' => $count,
							],
						],
					]
				]);
				?>
			</div>
		</div>
	</div>
	
	<div class="form-group well margin-top-30">
		<div class="row">
			<div class="col-md-12">
				<?php if (Yii::$app->user->can('comments.manage.index')) { ?>
					<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
						'class' => 'btn btn-default btn-lg'
					]) ?>
				<?php } ?>
			</div>
		</div>
	</div>

</div>