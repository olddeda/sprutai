<?php

namespace client\assets;

use Yii;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;

class FontAwesomeAsset extends AssetBundle
{
	public $sourcePath = '@bower/fontawesome';
	public $css = [
		'css/font-awesome.css',
	];
	
	/**
	 * Initializes the bundle.
	 * Set publish options to copy only necessary files (in this case css and font folders)
	 * @codeCoverageIgnore
	 */
	public function init()
	{
		parent::init();
		
		$this->publishOptions['beforeCopy'] = function ($from, $to) {
			return preg_match('%(/|\\\\)(fonts|css)%', $from);
		};
	}
}
