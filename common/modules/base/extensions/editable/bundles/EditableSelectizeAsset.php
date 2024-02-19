<?php
namespace common\modules\base\extensions\editable\bundles;

use yii\web\AssetBundle;

/**
 * Class EditableSelectizeAsset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class EditableSelectizeAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@common/modules/base/extensions/editable/assets/selectize';
	
	/**
	 * @var array
	 */
	public $css = [
		'bootstrap-editable-selectize.css',
	];
	
	/**
	 * @var array
	 */
	public $js = [
		'bootstrap-editable-selectize.js',
	];
	
	/**
	 * @var array
	 */
	public $depends = [
		'common\modules\base\extensions\editable\bundles\EditableBootstrapAsset',
		'common\modules\base\extensions\editable\bundles\SelectizeAsset',
	];
}
