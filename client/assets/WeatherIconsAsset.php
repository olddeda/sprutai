<?php

namespace client\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

class WeatherIconsAsset extends AssetBundle
{
	public $sourcePath = '@bower/weather-icons';
	public $css = [
		'css/weather-icons.css',
	];
}
