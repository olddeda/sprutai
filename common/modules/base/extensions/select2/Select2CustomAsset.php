<?php
namespace common\modules\base\extensions\select2;

use yii\web\AssetBundle;

/**
 * Class EditableSelect2Asset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class Select2CustomAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@common/modules/base/extensions/select2/assets';
	
	/**
	 * @var array
	 */
	public $css = [
		'select2-custom.css'
	];
	
	/**
	 * @var array
	 */
	public $js = [
		'select2-custom.js',
	];
}
