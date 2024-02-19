<?php
namespace common\modules\base\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

class i18nAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@bower/jquery-i18n';
	
	/**
	 * @var array
	 */
	public $js = [
		'jquery.i18n.js',
	];
}
