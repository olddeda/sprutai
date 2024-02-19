<?php

namespace client\assets;

use Yii;
use yii\web\AssetBundle;

class ScreenfullAsset extends AssetBundle
{
	public $sourcePath = '@bower/screenfull';
	public $js = [
		'dist/screenfull.js',
	];
}
