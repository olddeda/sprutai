<?php
namespace common\modules\menu\assets;

use yii\web\AssetBundle;

/**
 * Asset for the Tree JQuery plugin
 */
class TreeAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = __DIR__.'/assets';
	
	/**
	 * @var array
	 */
    public $css = [
        'jquery.tree.css',
    ];
	
	/**
	 * @var array
	 */
    public $js = [
        'jquery.tree.js',
		'jquery.tree.locale.js',
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
		'yii\jui\JuiAsset',
	
		'common\modules\base\assets\jQueryi18nAsset',
		'common\modules\base\extensions\select2\Select2Asset',
		'common\modules\base\extensions\select2\Select2CustomAsset',
    ];
}