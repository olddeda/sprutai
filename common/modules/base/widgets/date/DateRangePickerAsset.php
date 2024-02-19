<?php
namespace common\modules\base\widgets\date;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class DateRangePickerAsset
 * @package common\modules\base\widgets
 */
class DateRangePickerAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@bower/bootstrap-daterangepicker';
	
	/**
	 * @var array
	 */
    public $js = [
        'daterangepicker.js',
    ];
	
	/**
	 * @var array
	 */
    public $css = [
        'daterangepicker.css',
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
        MomentAsset::class,
    ];
}