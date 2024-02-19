<?php

namespace common\modules\media\widgets\imperavi;

use yii\web\AssetBundle;

/**
 * Crop asset bundle.
 */
class CropperAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@common/modules/media/widgets/imperavi/assets/cropper';

	/**
	 * @inheritdoc
	 */
	public $css = [
		'cropper.min.css'
	];

	/**
	 * @inheritdoc
	 */
	public $js = [
		'cropper.min.js'
	];

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\bootstrap\BootstrapAsset',
		'yii\bootstrap\BootstrapPluginAsset',
	];
}
