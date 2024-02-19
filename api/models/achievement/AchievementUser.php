<?php
namespace api\models\achievement;

use common\modules\achievement\models\AchievementUser as BaseModel;

use api\models\achievement\query\AchievementQuery;
use api\models\achievement\query\AchievementUserQuery;

use api\models\user\User;
use api\models\user\query\UserQuery;

/**
 * Class AchievementUser
 * @package api\models\achievement
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="achievement_id", type="integer", description="ID достижения"),
 *     @OA\Property(property="user_id", type="integer", description="ID пользователя"),
 *     @OA\Property(property="count", type="integer", description="Количество"),
 *     @OA\Property(property="created_at", type="string", description="Дата создания"),
 *     @OA\Property(property="updated_at", type="integer", description="Дата изменения"),
 *     @OA\Property(property="achievement", type="object", ref="#/components/schemas/Achievement", description="Достижение"),
 *     @OA\Property(property="user", type="object", ref="#/components/schemas/User", description="Пользователь")
 * )
 */
class AchievementUser extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'achievement_id',
            'user_id',
            'count',
            'created_at',
            'updated_at',
            'achievement',
            'user',
        ];
    }

    /**
     * @inheritdoc
     * @return AchievementUserQuery the active query used by this AR class.
     */
    public static function find() {
        return new AchievementUserQuery(get_called_class());
    }

    /**
     * @return AchievementQuery
     */
    public function getAchievement() {
        return $this->hasOne(Achievement::class, ['id' => 'achievement_id']);
    }

    /**
     * @return UserQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}