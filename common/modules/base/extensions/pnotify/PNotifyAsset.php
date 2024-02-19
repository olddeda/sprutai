<?php

namespace common\modules\base\extensions\pnotify;

use yii\web\AssetBundle;

/**
 * PNotify Asset Bundle.
 * 
 * @author Kevin LEVRON <kevin.levron@gmail.com>
 */
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
