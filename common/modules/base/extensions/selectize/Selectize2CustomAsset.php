<?php
namespace common\modules\base\extensions\selectize;

use Yii;
use yii\web\AssetBundle;

class Selectize2CustomAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@common/modules/base/extensions/selectize/assets';

	/**
	 * @var array
	 */
	public $js = [
		'js/preserve_on_blur.js',
	];
}