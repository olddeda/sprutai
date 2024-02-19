<?php
namespace common\modules\base\assets;

use yii\web\AssetBundle;

class ClipboardAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/base/assets/clipboard';
	
	/**
	 * @var array
	 */
    public $js = [
        'clipboard.js',
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
