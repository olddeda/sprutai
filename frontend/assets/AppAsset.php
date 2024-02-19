<?php
namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $basePath = '@webroot';
	
	/**
	 * @var string
	 */
	public $baseUrl = '@web';
	
	/**
	 * @var array
	 */
	public $css = [
		'css/site.css',
	];
	
	/**
	 * @var array
	 */
	public $js = [
		//'app.js'
	];
	
	/**
	 * @var array
	 */
	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
	];
}