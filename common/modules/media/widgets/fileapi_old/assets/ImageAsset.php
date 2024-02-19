<?php

namespace common\modules\media\widgets\fileapi\assets;

use yii\web\AssetBundle;

/**
 * Widget asset bundle.
 */
class ImageAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $depends = [
        'common\modules\media\widgets\fileapi\assets\FileAsset',
    ];
}
