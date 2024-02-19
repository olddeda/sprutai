<?php
namespace common\modules\queues\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;

/**
 * Class JobItemAsset
 * @package common\modules\queues\assets
 */
class JobItemAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/queues/web';
	
	/**
	 * @var array
	 */
    public $css = [
        'job-item.css',
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        BootstrapAsset::class,
    ];
}
