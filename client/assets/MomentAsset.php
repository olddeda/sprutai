<?php

namespace client\assets;

use Yii;
use yii\web\AssetBundle;

class MomentAsset extends AssetBundle
{
	public $sourcePath = '@bower/moment';
	public $js = [
		'min/moment-with-locales.min.js',
	];
}
