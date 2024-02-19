<?php
namespace api\models\user;

use common\modules\user\models\UserProfile as BaseUserProfile;
use Yii;

/**
 * Class UserProfile
 * @package api\models\user
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="user_id", type="integer", description="ID пользователя"),
 *     @OA\Property(property="first_name", type="string", description="Имя"),
 *     @OA\Property(property="last_name", type="string", description="Фамилия"),
 *     @OA\Property(property="middle_name", type="string", description="Отчество"),
 *     @OA\Property(property="data", type="object", description="Дополнительные данные")
 * )
 */
class UserProfile extends BaseUserProfile
{
	/**
	 * @inheritdoc
	 */
	public function fields() {
		return [
		    'user_id',
            'first_name',
            'last_name',
            'middle_name',
            'data' => function ($data) {
                if ($this->user_id == Yii::$app->user->id) {
                    return $this->data ? $this->data : [];
                }
                return [];
            }
        ];
	}
}