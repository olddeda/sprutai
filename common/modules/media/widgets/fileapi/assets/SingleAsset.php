<?php

namespace common\modules\media\widgets\fileapi\assets;

use yii\web\AssetBundle;

/**
 * Single upload asset bundle.
 */
class SingleAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@common/modules/media/widgets/fileapi/assets';

	/**
	 * @inheritdoc
	 */
	public $css = [
		'css/single.css'
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
		'common\modules\media\widgets\fileapi\Asset'
	];
}