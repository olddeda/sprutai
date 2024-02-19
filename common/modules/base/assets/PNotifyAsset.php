<?php
namespace common\modules\base\assets;

use Yii;
use yii\web\AssetBundle;

class PNotifyAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@bower/pnotify/src';

	/**
	 * @var array
	 */
	public $js = [
		'pnotify.core.js',
		'pnotify.buttons.js',
	];

	/**
	 * @var array
	 */
	public $css = [
		'pnotify.core.css',
		'pnotify.buttons.css',
	];

	/**
	 * @var array
	 */
	public $depends = [
		//'yii\web\JqueryAsset'
	];

}