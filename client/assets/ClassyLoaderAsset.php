<?php

namespace client\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

class ClassyLoaderAsset extends AssetBundle
{
	public $sourcePath = '@bower/classy-loader';
	public $js = [
		'jquery.classyloader.js',
	];
}
