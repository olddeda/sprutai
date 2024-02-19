<?php
namespace common\modules\base\extensions\phoneInput;

use yii\web\AssetBundle;

/**
 * Class EditableSelect2Asset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class PhoneInputCustomAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@common/modules/base/extensions/phoneInput/assets';
	
	/**
	 * @var array
	 */
	public $js = [
		'phoneInput-custom.js',
	];
}
