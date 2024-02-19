<?php
namespace common\modules\base\extensions\fileinput;

use common\modules\base\extensions\base\AssetBundle;

/**
 * Sortable asset bundle for FileInput Widget

 */
class SortableAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init() {
        $this->setSourcePath('@vendor/kartik-v/bootstrap-fileinput');
        $this->setupAssets('js', ['js/plugins/sortable']);
        parent::init();
    }
}