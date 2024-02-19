<?php

namespace common\modules\media\widgets\fileapi\widgets;

use yii\web\JsExpression;
use yii\helpers\Json;

use common\modules\media\widgets\fileapi\Widget;
use common\modules\media\widgets\fileapi\CropAsset;
use common\modules\media\widgets\fileapi\CropperAsset;
use common\modules\media\widgets\fileapi\assets\SingleAsset;

class ImageWidget extends Widget
{
	/**
	 * @var string Widget template view
	 *
	 * @see \yii\base\Widget::render
	 */
	public $template = 'image';

	/**
	 * Register widget asset.
	 */
	public function registerClientScript() {
		$view = $this->getView();
		$selector = $this->getSelector();

		SingleAsset::register($view);

		if ($this->preview === true) {

			// Add event handler for delete button
			$view->registerJs('fileapiDelete("'.$selector.'")');
		}

		if ($this->crop === true) {
			CropAsset::register($view);
			CropperAsset::register($view);
		}
	}

	/**
	 * Register default widget callbacks
	 */
	protected function registerDefaultCallbacks() {

		// File complete handler
		$this->callbacks['filecomplete'][] = new JsExpression('function (event, uiEvent) { fileapiComplete(this, event, uiEvent) }');

		if ($this->crop === true) {
			$view = $this->getView();
			$selector = $this->getSelector();

			// Get settings
			$cropperSettings = Json::encode($this->cropperSettings);

			// Add event handler for crop button
			$view->registerJs('fileapiCrop("'.$selector.'")');

			// Add event handler for cancel button
			$view->registerJs('fileapiClose("'.$selector.'")');

			// Crop event handler
			$this->callbacks['select'] = new JsExpression('function (evt, ui) { fileapiSelect('.$cropperSettings.', ui.files[0]) }');
		}
	}
}