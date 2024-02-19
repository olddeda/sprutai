<?php
namespace common\modules\base\widgets\youtube;

use yii\web\AssetBundle;

/**
 * Class YoutubeAsset
 * @package common\modules\base\widgets\youtube
 */
class YoutubeAsset extends AssetBundle
{
	/**
	 * @var array
	 */
    public $js = [
        'https://www.youtube.com/iframe_api'
    ];
	
	/**
	 * @var array
	 */
    public $jsOptions = [
        'position'=>\yii\web\View::POS_HEAD
    ];
}