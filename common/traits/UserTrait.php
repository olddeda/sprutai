<?php

namespace common\traits;

trait UserTrait
{
	use UtilsTrait;

	/**
	 * Get image info
	 * @return string
	 */
	protected function getImageDir() {
		return self::getDir(array(
			'client_id' => $this->id,
			'path' => 'user',
		));
	}

	/**
	 * Get image path
	 * @return string|bool
	 */
	public function getImagePath() {
		if ($this->getImageDir())
			return $this->getImageDir()['filesys'];
		return false;
	}

	/**
	 * Get image
	 * @return string
	 */
	protected function getImageAvatar($http = true) {
		return $this->getImageInfo('avatar.jpg', $this->image_avatar_time, $http);
	}
}