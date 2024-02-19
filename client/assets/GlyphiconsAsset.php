<?php

namespace client\assets;

use Yii;
use yii\web\AssetBundle;

class GlyphiconsAsset extends AssetBundle
{
	public $sourcePath = '@bower/glyphicons';
	
	public $css = [
		'styles/glyphicons.css',
	];
}
