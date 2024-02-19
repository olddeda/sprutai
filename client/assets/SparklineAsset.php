<?php

namespace client\assets;

use Yii;
use yii\web\AssetBundle;

class SparklineAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@bower/jquery.sparkline';
	
	/**
	 * @var array
	 */
	public $js = [
		'dist/jquery.sparkline.js',
	];
}
