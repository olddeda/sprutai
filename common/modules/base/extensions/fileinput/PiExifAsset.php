<?php
namespace common\modules\base\extensions\fileinput;

use common\modules\base\extensions\base\AssetBundle;

/**
 * PiExif Asset bundle for FileInput Widget
 */
class PiExifAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init() {
        $this->setSourcePath('@vendor/kartik-v/bootstrap-fileinput');
        $this->setupAssets('js', ['js/plugins/piexif']);
        parent::init();
    }
}
