<?php

namespace common\modules\base\extensions\selectize;

use yii\web\AssetBundle;

/**
 * Class SelectizeAsset
 * @package common\extensions\selectize
 */
class SelectizeAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@bower/selectize/dist';
	
	/**
	 * @var array
	 */
	public $css = [
		'css/selectize.bootstrap3.css',
	];
	
	/**
	 * @var array
	 */
	public $js = [
		'js/standalone/selectize.js',
	];
	
	/**
	 * @var array
	 */
	public $depends = [
		'yii\web\YiiAsset',
		'yii\web\JqueryAsset',
	];
}
