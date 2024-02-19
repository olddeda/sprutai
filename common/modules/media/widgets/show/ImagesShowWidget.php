<?php

namespace common\modules\media\widgets\show;

use Yii;
use yii\base\Widget;

use common\modules\media\helpers\enum\Mode;

class ImagesShowWidget extends Widget
{
	/** @var */
	public $images;

	/** @var integer $width */
	public $width;

	/** @var integer $height */
	public $height;

	/** @var */
	public $mode = Mode::CROP_CENTER;

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
		return $this->render('images-show', [
			'images' => $this->images,
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