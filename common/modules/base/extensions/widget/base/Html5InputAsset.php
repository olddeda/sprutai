<?php

namespace common\modules\base\extensions\widget\base;

/**
 * Asset bundle for Html5Input Widget
 *
 * @since 1.0
 */
class Html5InputAsset extends \kartik\base\AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public function init() {
		$this->setSourcePath(__DIR__.'/assets');
		$this->setupAssets('css', ['css/html5input']);
		parent::init();
	}
}
