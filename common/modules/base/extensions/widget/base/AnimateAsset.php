<?php

namespace common\modules\base\extensions\widget\base;

/**
 * Asset bundle for loading animations
 *
 * @since 1.0
 */
class AnimateAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init() {
        $this->setSourcePath(__DIR__.'/assets');
        $this->setupAssets('css', ['css/animate']);
        parent::init();
    }
}
