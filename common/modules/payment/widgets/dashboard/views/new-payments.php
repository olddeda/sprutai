<?php

use yii\web\JsExpression;

use conquer\flot\FlotWidget;

use common\modules\payment\helpers\enum\Status;

?>

<?= FlotWidget::widget([
	'htmlOptions' => [
		'class' => 'flot-chart',
	],
	'data' => [
		[
			'label' => Status::getLabel(Status::PAID),
			'data' => $data[Status::PAID],
			'color' => '#768294',
		],
		[
			'label' => Status::getLabel(Status::FAILED),
			'data' => $data[Status::FAILED],
			'color' => '#1f92fe',
		],
		//[
		//	'label' => Status::getLabel(Status::WAIT),
		//	'data' => $data[Status::WAIT],
		//	'color' => '#aad874',
		//],
	],
	'options' => [
		'series' => [
			'lines' => [
				'show' => true,
				'fill' => 0.5,
			],
			'points' => [
				'show' => true,
                'radius' => 4,
            ],
			'splines' => [
				'show' => false,
                'tension' => 0.4,
                'lineWidth' => 1,
                'fill' => 0.5,
			],
		],
		'legend' => [
			'hideable' => true,
    	],
		'grid' => [
			'borderColor' => '#eee',
			'borderWidth' => 1,
			'hoverable' => true,
            'backgroundColor' => '#fcfcfc'
		],
		'tooltip' => true,
		'tooltipOpts' => [
			'content' => new JsExpression("
				function (label, x, y) {
					return '<b>' + label + '</b><br>' + flotDateFormat(x / 1000, '".$format."', '".Yii::$app->language."') + ' - ' + y;
				}
			"),
		],
		'xaxis' => [
			'tickColor' => '#fcfcfc',
            'mode' => 'time',
			'minTickSize' => [1, $period],
			'timezone' => 'browser',
			'tickFormatter' => new JsExpression("
                function (v) {
                    return flotDateFormat(v / 1000, '".$format."', '".Yii::$app->language."') + '&nbsp;'
                }
            "),
        ],
		'yaxis' => [
			'min' => 0,
            'max' => $max + ceil($max / 100 * 50),
            'tickColor' => '#eee',
			'tickDecimals' => 0,
            'tickFormatter' => new JsExpression("
                function (v) {
                    return v + '&nbsp;'
                }
            "),
        ],
		'shadowSize' => 0,
	],
	'plugins' => [
		'jquery.flot.time.js',
	],
]); ?>