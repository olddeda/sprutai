<?php
namespace common\modules\base\extensions\editable\bundles;

use yii\web\AssetBundle;

/**
 * Class EditableSelect2Asset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class EditableSelect2Asset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@common/modules/base/extensions/editable/assets/select2';
	
	/**
	 * @var array
	 */
	public $css = [
		'bootstrap-editable-select2.css',
	];
	
	/**
	 * @var array
	 */
	public $js = [
		'bootstrap-editable-select2.js',
	];
	
	/**
	 * @var array
	 */
	public $depends = [
		'common\modules\base\extensions\editable\bundles\EditableBootstrapAsset',
		'common\modules\base\extensions\editable\bundles\Select2Asset',
	];
}
