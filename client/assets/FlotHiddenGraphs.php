<?php
namespace client\assets;

use yii\web\AssetBundle;

class FlotHiddenGraphs extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';
	
	public $css = [
	];
	
	public $js = [
		'js/jquery.flot.hiddengraphs.js',
	];
	
	public $depends = [
		'client\assets\FlotAsset',
	];
}
