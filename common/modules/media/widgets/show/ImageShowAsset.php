<?php

namespace common\modules\media\widgets\show;

use yii\web\AssetBundle;

/**
 * Class ImageShowAsset
 * @package common\modules\comments
 */
class ImageShowAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@common/modules/media/widgets/show/assets';

	/**
	 * @inheritdoc
	 */
	public $js = [];

	/**
	 * @inheritdoc
	 */
	public $css = [
		'image-show-widget.css'
	];
}