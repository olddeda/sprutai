<?php

namespace client\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

class WhirlAsset extends AssetBundle
{
	public $sourcePath = '@bower/whirl';
	public $css = [
		'dist/whirl.css',
	];
}
