<?php

namespace common\modules\base\extensions\widget\fileinput;

use common\modules\base\extensions\widget\base\AssetBundle;

/**
 * Asset bundle for FileInput Widget
 *
 * @since 1.0
 */
class FileInputAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
    public function init() {
        $this->setSourcePath(__DIR__.'/assets/bootstrap-fileinput');
        $this->setupAssets('css', ['css/fileinput']);
        $this->setupAssets('js', ['js/fileinput']);
        parent::init();
    }
}
