<?php

namespace client\assets;

use Yii;

use yii\web\AssetBundle;

class AceAsset extends AssetBundle
{
	public $sourcePath = '@bower/ace';
	
	public $js = [
		'build/src/ace.js',
	];
}
