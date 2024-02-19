<?php
namespace common\modules\base\widgets\date;

use yii\web\AssetBundle;

/**
 * Class MomentAsset
 * @package common\modules\base\widgets
 */
class MomentAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@bower/moment/min';
	
	/**
	 * @var array
	 */
    public $js = [
        'moment-with-locales.min.js',
    ];
}