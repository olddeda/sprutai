<?php
namespace api\models\user;

use common\modules\user\models\UserToken as BaseModel;

/**
 * Class UserToken
 * @package api\models\user
 */
class UserToken extends BaseModel
{
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}
}