<?php
namespace common\modules\project\assets;

use yii\web\AssetBundle;

/**
 * Class Asset
 * @package common\modules\project\assets
 */
class Asset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@common/modules/project/assets';

	/**
	 * @inheritdoc
	 */
	public $js = [
		'js/project.js'
	];

	/**
	 * @inheritdoc
	 */
	public $css = [
		'css/project.css'
	];

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\web\JqueryAsset',
		'yii\web\YiiAsset'
	];
}