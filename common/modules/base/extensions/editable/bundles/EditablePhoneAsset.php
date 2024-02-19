<?php
namespace common\modules\base\extensions\editable\bundles;

use yii\web\AssetBundle;

/**
 * Class EditablePhoneAsset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class EditablePhoneAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@common/modules/base/extensions/editable/assets/phone';
	
	/**
	 * @var array
	 */
	public $css = [
		'bootstrap-editable-phone.css',
	];
	
	/**
	 * @var array
	 */
	public $js = [
		'bootstrap-editable-phone.js',
	];
	
	/**
	 * @var array
	 */
	public $depends = [
		'common\modules\base\extensions\editable\bundles\EditableBootstrapAsset',
		'common\modules\base\extensions\editable\bundles\PhoneInputAsset',
	];
}
