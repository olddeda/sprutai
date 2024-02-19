<?php

namespace client\assets;

use Yii;
use yii\web\AssetBundle;

class FlotTooltipAsset extends AssetBundle
{
	public $sourcePath = '@bower/flot.tooltip';
	
	public $js = [
		'js/jquery.flot.tooltip.js',
	];
	
	public $depends = [
		'client\assets\FlotAsset',
	];
}