<?php
namespace common\modules\base\extensions\gridstack;

use yii\web\AssetBundle;

/**
 * Asset for the Gridstack JQuery plugin
 */
class GridstackAsset extends AssetBundle 
{
    public $sourcePath = '@common/modules/base/extensions/gridstack/assets';

    public $css = [
        'gridstack.css',
        'gridstack.utils.css',
    ];

    public $js = [
        'gridstack.js',
		'gridstack.jQueryUI.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
		
        'common\modules\base\extensions\gridstack\LodashAsset',
    ];
}