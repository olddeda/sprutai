<?php

namespace client\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

class FlotAsset extends AssetBundle
{
	public $sourcePath = '@bower/flot';
	
	public $js = [
		'jquery.flot.js',
		'jquery.flot.resize.js',
		'jquery.flot.pie.js',
		'jquery.flot.time.js',
		'jquery.flot.categories.js',
	];
}
