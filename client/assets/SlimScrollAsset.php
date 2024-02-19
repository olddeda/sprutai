<?php

namespace client\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

class SlimScrollAsset extends AssetBundle
{
	public $sourcePath = '@bower/slimscroll';
	public $js = [
		'jquery.slimscroll.js',
	];
}
