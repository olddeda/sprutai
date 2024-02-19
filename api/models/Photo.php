<?php

namespace api\models;

use common\modules\media\models\Photo as BasePhoto;

/**
 * Class Photo
 * @package api\models
 */
class Photo extends BasePhoto
{
	/**
	 * @inheritdoc
	 *
	 * The default implementation returns the names of the columns whose values have been populated into this record.
	 */
	public function fields() {
		return ['id', 'parent_type', 'parent_id', 'user_id', 'title', 'status', 'created_at', 'updated_at', 'image'];
	}

	/**
	 * Get image
	 * @return array
	 */
	public function getImage($http = true) {
		$image = parent::getImage($http);
		return ($image) ? $image : [
			'url' => null,
			'path' => null,
			'file' => null,
		];
	}
}