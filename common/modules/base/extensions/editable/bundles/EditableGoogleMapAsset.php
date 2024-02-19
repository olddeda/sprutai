<?php
namespace common\modules\base\extensions\editable\bundles;

use yii\web\AssetBundle;

/**
 * Class EditableGoogleMapAsset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class EditableGoogleMapAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@common/modules/base/extensions/editable/assets/googlemap';
	
	/**
	 * @var array
	 */
	public $css = [
		'bootstrap-editable-googlemap.css',
	];
	
	/**
	 * @var array
	 */
	public $js = [
		'//maps.google.com/maps/api/js?libraries=places&key=AIzaSyBd4HCOwQuOLi30JHle8Bd0Z1Nx3NAd7LU',
		'vendor/js/locationpicker.jquery.js',
		'vendor/js/jquery.placepicker.js',
		'bootstrap-editable-googlemap.js',
	];
	
	/**
	 * @var array
	 */
	public $depends = [
		'common\modules\base\extensions\editable\bundles\EditableBootstrapAsset',
	];
}
