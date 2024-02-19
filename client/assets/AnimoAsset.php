<?php

namespace client\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

class AnimoAsset extends AssetBundle
{
	public $sourcePath = '@bower/animo';
	
	public $js = [
		'animo.js',
	];
}
