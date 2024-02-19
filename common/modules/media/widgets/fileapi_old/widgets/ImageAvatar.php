<?php

namespace common\modules\media\widgets\fileapi\widgets;

use common\modules\media\widgets\fileapi\assets\ImageAvatarAsset;
use common\modules\media\widgets\fileapi\CropAsset;

use yii\helpers\Json;

class ImageAvatar extends Image
{
	/**
	 * @var boolean Enable/disable files preview
	 */
	public $preview = true;

	/**
	 * @var boolean Enable/disable crop
	 */
	public $crop = true;

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
	 * @var string Widget template view
	 *
	 * @see \yii\base\Widget::render
	 */
	public $template = 'file_avatar';

	/**
	 * Register widget asset.
	 */
	public function registerClientScript() {
		$view = $this->getView();

		ImageAvatarAsset::register($view);

		if ($this->crop === true)
			CropAsset::register($view);

		$options = Json::encode($this->settings);

		$view->registerJs('jQuery("#'.$this->settings['selector'].'").yiiMediaFileAPI("image", '.$options.');');
	}
}
