<?php

namespace common\modules\media\widgets\imperavi;

use yii\web\AssetBundle;

/**
 * Widget asset bundle
 */
class Asset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@common/modules/media/widgets/imperavi/assets';

	/**
	 * @var string Redactor language
	 */
	public $language;

	/**
	 * @var array Redactor plugins array
	 */
	public $plugins = [];

	/**
	 * @inheritdoc
	 */
	public $css = [
		'redactor.css'
	];

	/**
	 * @inheritdoc
	 */
	public $js = [
		'redactor.js'
	];

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\web\JqueryAsset',
		'common\modules\media\widgets\imperavi\CropperAsset',
	];

	/**
	 * Register asset bundle language files and plugins.
	 */
	public function registerAssetFiles($view) {
		if ($this->language !== null) {
			$this->js[] = 'lang/'.$this->language.'.js';
		}
		if (!empty($this->plugins)) {
			foreach ($this->plugins as $plugin) {
				if ($plugin === 'clips') {
					$this->css[] = 'plugins/'.$plugin.'/'.$plugin.'.css';
				}
				$this->js[] = 'plugins/'.$plugin.'/'.$plugin.'.js';
			}
		}
		parent::registerAssetFiles($view);
	}
}
