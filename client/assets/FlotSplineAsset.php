<?php

namespace client\assets;

use Yii;
use yii\web\AssetBundle;

class FlotSplineAsset extends AssetBundle
{
	public $sourcePath = '@bower/flot-spline';
	
	public $js = [
		'js/jquery.flot.spline.js',
	];
	
	public $depends = [
		'client\assets\FlotAsset',
	];
}