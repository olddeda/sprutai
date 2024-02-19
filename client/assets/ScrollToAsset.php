<?php
namespace client\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

class ScrollToAsset extends AssetBundle
{
	public $sourcePath = '@bower/jquery.scrollto';
	public $js = [
		'jquery.scrollTo.js',
	];
}
