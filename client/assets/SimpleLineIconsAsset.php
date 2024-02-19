<?php

namespace client\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

class SimpleLineIconsAsset extends AssetBundle
{
	public $sourcePath = '@bower/simple-line-icons';
	public $css = [
		'css/simple-line-icons.css',
	];
}
