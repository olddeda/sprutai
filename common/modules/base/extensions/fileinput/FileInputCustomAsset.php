<?php
namespace common\modules\base\extensions\fileinput;

use yii\web\AssetBundle;

/**
 * Class SelectizeAsset
 * @package common\extensions\selectize
 */
class FileInputCustomAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = __DIR__.'/assets';
	
	/**
	 * @var array
	 */
	public $css = [
		'fileinput-custom.css'
	];
}
