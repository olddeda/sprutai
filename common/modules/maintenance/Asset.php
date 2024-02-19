<?php
namespace common\modules\maintenance;

use yii\web\AssetBundle;

/**
 * Class Asset
 * @package common\modules\maintenance
 */
class Asset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@common/modules/maintenance/assets';
    /**
     * @inheritdoc
     */
    public $css = [
        YII_ENV_DEV ? 'css/styles.css' : 'css/styles.min.css',
    ];
    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\YiiAsset',
    ];
}