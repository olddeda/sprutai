<?php

namespace common\modules\media\widgets\fileapi\assets;

use yii\web\AssetBundle;

/**
 * Avatar upload asset bundle.
 */
class AvatarAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@common/modules/media/widgets/fileapi/assets';

	/**
	 * @inheritdoc
	 */
	public $css = [
		'css/avatar.css',
		'css/cropper.css',
	];

	/**
	 * @inheritdoc
	 */
	public $js = [
		'js/jquery.fileapi.js',
		'js/jquery.fileapi.utils.js',
	];

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'common\modules\media\widgets\fileapi\Asset',
	];
}
