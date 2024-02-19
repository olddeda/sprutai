<?php

namespace api\modules\v1\models;

use api\models\User as BaseUser;
use api\modules\v1\models\UserProfile;

/**
 * Class User
 * @package api\modules\v1\models
 */
class User extends BaseUser
{
	/**
	 * Get profile
	 * @return \yii\db\ActiveQuery
	 */
	public function getProfile() {
		return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
	}
}