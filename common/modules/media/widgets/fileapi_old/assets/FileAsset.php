<?php

namespace common\modules\media\widgets\fileapi\assets;

use yii\web\AssetBundle;

/**
 * Widget asset bundle.
 */
class FileAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@common/modules/media/widgets/fileapi/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'css/media-widget.css'
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/media-fileAPI.js'
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'common\modules\media\widgets\fileapi\Asset',
    ];
}
