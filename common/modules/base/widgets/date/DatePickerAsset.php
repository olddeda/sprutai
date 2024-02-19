<?php
namespace common\modules\base\widgets\date;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class DatePickerAsset
 * @package common\modules\base\widgets
 */
class DatePickerAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@bower/bootstrap-datepicker/dist';
	
	/**
	 * @var array
	 */
    public $js = [
        'js/bootstrap-datepicker.js',
    ];
	
	/**
	 * @var array
	 */
    public $css = [
        'css/bootstrap-datepicker3.css',
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
    ];
}