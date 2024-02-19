<?php

namespace common\modules\media\models;

use Yii;

use common\modules\media\models\query\MediaImageQuery;

/**
 * Class MediaImage
 * @package common\modules\media\models
 *
 * @property-read string $width_and_height
 */
class MediaImage extends Media
{
	public $image;

	/**
	 * @inheritdoc
	 * @return MediaImageQuery the active query used by this AR class.
	 */
	public static function find()
    {
		return new MediaImageQuery(get_called_class());
	}

	/**
	 * Get width and height
	 * @return string
	 */
	public function getWidth_and_height()
    {
		return $this->width.'x'.$this->height;
	}
}