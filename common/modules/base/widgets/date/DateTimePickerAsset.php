<?php
namespace common\modules\base\widgets\date;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class DateTimePickerAsset
 * @package common\modules\base\widgets
 */
class DateTimePickerAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@bower/smalot-bootstrap-datetimepicker';
	
	/**
	 * @var array
	 */
    public $js = [
        'js/bootstrap-datetimepicker.js',
    ];
	
	/**
	 * @var array
	 */
    public $css = [
        'css/bootstrap-datetimepicker.css',
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
    ];
}