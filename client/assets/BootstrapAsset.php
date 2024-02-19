<?php

namespace client\assets;

use Yii;

use yii\web\AssetBundle;

class BootstrapAsset extends AssetBundle
{
	public $sourcePath = '@bower/bootstrap/dist';
	
	public $js = [
		'js/bootstrap.js',
	];
	
	public $css = [
		'css/bootstrap.css',
	];
}
