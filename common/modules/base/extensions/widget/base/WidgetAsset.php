<?php

namespace common\modules\base\extensions\widget\base;

use Yii;

/**
 * Common base widget asset bundle
 *
 * @since 1.0
 */
class WidgetAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public function init() {
		$this->setSourcePath(__DIR__.'/assets');
		$this->setupAssets('css', ['css/appmake-widget']);
		parent::init();
	}
}
