<?php

namespace client\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

class ModernizrAsset extends AssetBundle
{
	public $sourcePath = '@bower/modernizr';
	public $js = [
		'modernizr.js',
	];
}
