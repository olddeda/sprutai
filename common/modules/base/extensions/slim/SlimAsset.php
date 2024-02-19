<?php
namespace common\modules\base\extensions\slim;

use yii\web\AssetBundle;

class SlimAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/base/extensions/slim/assets';
	
	/**
	 * @var array
	 */
    public $js = [
	    'slim.jquery.js',
    ];
	
	/**
	 * @var array
	 */
    public $css = [
        'slim.css',
		'slim.custom.css'
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
