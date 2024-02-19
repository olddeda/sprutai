<?php
namespace common\modules\base\extensions\fileinput;

use common\modules\base\extensions\base\AssetBundle;

/**
 * DomPurify asset bundle for FileInput Widget
 */
class DomPurifyAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init() {
        $this->setSourcePath('@vendor/kartik-v/bootstrap-fileinput');
        $this->setupAssets('js', ['js/plugins/purify']);
        parent::init();
    }
}