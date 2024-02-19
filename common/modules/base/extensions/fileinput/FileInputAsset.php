<?php
namespace common\modules\base\extensions\fileinput;

use common\modules\base\extensions\base\AssetBundle;

/**
 * Asset bundle for FileInput Widget
 */
class FileInputAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init() {
        $this->setSourcePath('@vendor/kartik-v/bootstrap-fileinput');
        $this->setupAssets('css', ['css/fileinput']);
        $this->setupAssets('js', ['js/fileinput']);
        parent::init();
    }
}
