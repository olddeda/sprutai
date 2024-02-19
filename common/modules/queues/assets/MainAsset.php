<?php
namespace common\modules\queues\assets;

use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\BootstrapThemeAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Class MainAsset
 * @package common\modules\queues\assets
 */
class MainAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/queues/web';
	
	/**
	 * @var array
	 */
    public $css = [
        'main.css',
    ];
	
	/**
	 * @var array
	 */
    public $js = [];
	
	/**
	 * @var array
	 */
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        BootstrapThemeAsset::class,
    ];
}
