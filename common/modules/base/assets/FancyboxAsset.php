<?php
namespace common\modules\base\assets;

use Yii;
use yii\web\AssetBundle;

class FancyboxAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@bower/fancybox/dist';

	/**
	 * @var array
	 */
	public $js = [
		'jquery.fancybox.js',
	];

	/**
	 * @var array
	 */
	public $css = [
		'jquery.fancybox.css',
	];

	/**
	 * @var array
	 */
	public $depends = [
		'yii\web\JqueryAsset'
	];

}