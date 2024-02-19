<?php
namespace common\modules\base\extensions\wizardwidget;

use yii\web\AssetBundle;

/**
 * Asset bundle for Wizard Widget
 */
class WizardWidgetAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@common/modules/base/extensions/wizardwidget';
	
	/**
	 * @var array
	 */
	public $css = [
		'css/wizardwidget.css',
	];
	
	/**
	 * @var array
	 */
	public $js = [
		'js/wizardwidget.js'
	];
	
	/**
	 * @var array
	 */
	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapPluginAsset'
	];
}