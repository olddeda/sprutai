<?php
namespace common\modules\base\assets;

use yii\web\AssetBundle;

class PrismAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/base/assets/prism';
	
	/**
	 * @var array
	 */
    public $js = [
        'prism.js',
    ];
	
	/**
	 * @var array
	 */
    public $css = [
        'prism.css',
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
