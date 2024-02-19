<?php
namespace common\modules\media\widgets\fileinput;

use yii\web\AssetBundle;

/**
 * Class SelectizeAsset
 * @package common\extensions\selectize
 */
class ImageWidgetAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = __DIR__.'/assets';
	
	/**
	 * @var array
	 */
	public $css = [
		'css/image-widget.css'
	];
	
	/**
	 * @var array
	 */
	public $js = [
		'js/image-widget.js',
	];
}
