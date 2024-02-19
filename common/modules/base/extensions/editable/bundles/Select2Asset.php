<?php
namespace common\modules\base\extensions\editable\bundles;

use yii\web\AssetBundle;

/**
 * Class Select2Asset
 *
 * @package common\modules\base\extensions\editable\bundles
 */
class Select2Asset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@bower/select2/dist';
	
	/**
	 * @var array
	 */
	public $js = [
		'js/select2.full.min.js',
	];
	
	/**
	 * @var array
	 */
	public $css = [
		'css/select2.min.css',
	];
	
	/**
	 * @var array
	 */
	public $depends = [
		'yii\web\JqueryAsset',
		'yii\bootstrap\BootstrapAsset',
	];
	
	/**
	 * @inheritdoc
	 */
	public function registerAssetFiles($view) {
		$language = \Yii::$app->language;
		
		if (is_file(\Yii::getAlias("{$this->sourcePath}/js/i18n/{$language}.js"))) {
			$this->js[] = "js/i18n/{$language}.js";
		}
		parent::registerAssetFiles($view);
	}
}
