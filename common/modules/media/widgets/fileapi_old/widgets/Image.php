<?php

namespace common\modules\media\widgets\fileapi\widgets;

use common\modules\media\widgets\fileapi\assets\ImageAsset;
use common\modules\media\widgets\fileapi\CropAsset;

use yii\helpers\Json;

class Image extends File
{
	/**
	 * @var boolean Enable/disable files preview
	 */
	public $preview = true;

	/**
	 * @var boolean Enable/disable crop
	 */
	public $crop = false;

	/**
	 * @var array JCrop settings
	 */
	public $jcropSettings = [
		'aspectRatio' => 1,
		'bgColor' => '#ffffff',
		'minSize' => [100, 100],
		'maxSize' => [1000, 1000],
		'keySupport' => false,

		// Important param to hide jCrop radio button.
		'selection' => '100%'
	];

	/**
	 * Register widget asset.
	 */
	public function registerClientScript() {
		$view = $this->getView();

		ImageAsset::register($view);

		if ($this->crop === true)
			CropAsset::register($view);

		$selector = $this->getSelector();
		$options = Json::encode($this->settings);

		$view->registerJs("jQuery('#$selector').yiiMediaFileAPI('image', $options);");
	}
}
