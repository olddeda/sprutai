<?php
namespace common\modules\base\extensions\locationpicker;

use yii\web\View;
use yii\web\AssetBundle;

/**
 * LocationPickerAsset
 */
class LocationPickerAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/base/extensions/locationpicker/assets';
	
	/**
	 * @var array
	 */
    public $css = [
    ];
	
	/**
	 * @var array
	 */
    public $js = [
        'locationpicker.jquery.js',
    ];
	
	/**
	 * @var array
	 */
    public $jsOptions = [
        'position' => View::POS_END,
    ];
    
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
