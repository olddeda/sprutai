<?php

namespace common\modules\base\extensions\editable\bundles;

use yii\web\AssetBundle;

/**
 * Class EditablePriceAsset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class EditablePriceAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@common/modules/base/extensions/editable/assets/price';
	
	/**
	 * @var array
	 */
	public $css = [
		'bootstrap-editable-price.css',
	];
	
	/**
	 * @var array
	 */
	public $js = [
		'bootstrap-editable-price.js',
	];
	
	/**
	 * @var array
	 */
	public $depends = [
		'common\modules\base\extensions\editable\bundles\EditableBootstrapAsset',
	];
}
