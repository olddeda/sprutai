<?php

namespace client\assets;

use Yii;
use yii\web\AssetBundle;

class MatchMediaAsset extends AssetBundle
{
	public $sourcePath = '@bower/matchmedia';
	public $js = [
		'matchMedia.js',
	];
}
