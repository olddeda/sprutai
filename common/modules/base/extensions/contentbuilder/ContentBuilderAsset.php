<?php
namespace common\modules\base\extensions\contentbuilder;

use yii\web\AssetBundle;

class ContentBuilderAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/base/extensions/contentbuilder/vendor/contentbuilder';
	
	/**
	 * @var array
	 */
    public $js = [
    	'i18n/ru/i18n-contentbuilder.js',
	    'contentbuilder.setup.js',
        'contentbuilder.src.js',
	    'saveimages.js',
    ];
	
	/**
	 * @var array
	 */
    public $css = [
        'contentbuilder.css',
	    'contentbuilder.plugin.css'
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
	    'yii\jui\JuiAsset',
	    'common\modules\base\assets\jQueryi18nAsset',
	    'common\modules\base\assets\CodeMirrorAsset',
	    'common\modules\base\extensions\select2\Select2Asset'
    ];
}
