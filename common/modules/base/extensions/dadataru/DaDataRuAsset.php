<?php
namespace common\modules\base\extensions\dadataru;

use yii\web\AssetBundle;

class DaDataRuAsset extends AssetBundle
{
	/**
	 * @var array
	 */
    public $js = [
	    '//cdn.jsdelivr.net/npm/suggestions-jquery@19.2.0/dist/js/jquery.suggestions.min.js',
    ];
	
	/**
	 * @var array
	 */
    public $css = [
    	'//cdn.jsdelivr.net/npm/suggestions-jquery@19.2.0/dist/css/suggestions.min.css',
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
	    'common\modules\base\extensions\dadataru\DaDataRuAssetIE10'
    ];
}
