<?php

namespace client\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

class AnimateCssAsset extends AssetBundle
{
	public $sourcePath = '@bower/animate.css';
	public $css = [
		'animate.css',
	];
}
