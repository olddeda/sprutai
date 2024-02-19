<?php

namespace common\modules\media\widgets\fileapi;

use yii\web\AssetBundle;

/**
 * Crop asset bundle.
 */
class CropperAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@bower/cropper';

	/**
	 * @inheritdoc
	 */
	public $css = [
		'dist/cropper.min.css'
	];

	/**
	 * @inheritdoc
	 */
	public $js = [
		'dist/cropper.min.js'
	];

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'common\modules\media\widgets\fileapi\Asset',

		'yii\bootstrap\BootstrapAsset',
		'yii\bootstrap\BootstrapPluginAsset',
	];
}
