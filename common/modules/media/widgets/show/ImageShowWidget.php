<?php

namespace common\modules\media\widgets\show;

use Yii;
use yii\base\Widget;

use common\modules\media\helpers\enum\Mode;

class ImageShowWidget extends Widget
{
	/** @var object */
	public $model;

	/** @var integer $width */
	public $width;

	/** @var integer $height */
	public $height;

	/** @var integer */
	public $mode = Mode::CROP_CENTER;
	
	/** @var bool */
	public $multiple = false;

	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();

		$this->registerAssets();
	}

	/**
	 * @inheritdoc
	 */
	public function run() {
		return $this->render(($this->multiple ? 'images-show' : 'image-show'), [
			'model' => $this->model,
			'width' => $this->width,
			'height' => $this->height,
			'mode' => $this->mode,
		]);
	}

	/**
	 * Register assets.
	 */
	protected function registerAssets() {
		$view = $this->getView();
		ImageShowAsset::register($view);
	}
}