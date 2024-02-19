<?php
namespace common\modules\base\extensions\contentbuilder;

use yii\web\AssetBundle;

class ContentBuilderSlickAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/base/extensions/contentbuilder/vendor/slick';
	
	/**
	 * @var array
	 */
    public $js = [
        'slick.js',
    ];
	
	/**
	 * @var array
	 */
    public $css = [
        'slick.css',
	    'slick-theme.css'
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
