<?php

use yii\helpers\Html;

use dosamigos\chartjs\ChartJs;

use common\modules\media\models\MediaImage;

$this->title = Yii::t('media', 'title');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('canvas {width: 100% !important;height: 400px;}');
?>
<div class="media-index">
	<div class="row">
		<div class="col-md-12 col-lg-12">
			<h2><?php echo Html::a(Yii::t('media-image', 'title'), ['/media/image/index']); ?></h2>

			<div class="well">
				<?php
				$days = [];
				$count = [];
				foreach (range(-6, 0) as $day) {
					$date = strtotime($day . 'days');
					$days[] = Yii::$app->formatter->asDate($date, 'php:d M Y'); //date('D: Y-m-d', $date);
					$count[] = MediaImage::find()->andWhere(['between', 'created_at', strtotime(date('Y-m-d 00:00:00', $date)), strtotime(date('Y-m-d 23:59:59', $date))])->count();
				}
				echo ChartJs::widget([
					'type' => 'bar',
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

</div>