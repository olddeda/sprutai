<?php

namespace api\modules\v1\models;

use api\models\UserToken as BaseUserToken;

/**
 * Class UserToken
 * @package api\modules\v1\models
 */
class UserToken extends BaseUserToken
{
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}