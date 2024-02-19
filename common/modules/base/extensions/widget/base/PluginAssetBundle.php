<?php

namespace common\modules\base\extensions\widget\base;

/**
 * Base asset bundle
 *
 * @since 1.0
 */
class PluginAssetBundle extends AssetBundle
{
    /**
     * @inheritdoc
     */
     public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
    