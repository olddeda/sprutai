<?php
namespace common\modules\eav\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Class FbAsset
 * @package common\modules\eav\assets
 */
class FbAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $baseUrl = '@web';
	
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/eav/assets/formbuilder';
	
	/**
	 * @var array
	 */
    public $css = [
        'css/vendor.css',
        'css/formbuilder.css',
    ];
	
	/**
	 * @var array
	 */
    public $js = [
        'js/underscorejs.js',
        'js/backbone.js',
        'js/jquery.ui.js',
        'js/app.js',
        'js/rivets.js',
        'js/formbuilder.js',
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}