<?php
namespace api\models\favorite;

use yii\db\ActiveQuery;

use common\modules\favorite\models\Favorite as BaseModel;

use api\models\favorite\FavoriteGroup;

use api\models\user\User;

/**
 * Class Favorite
 * @package api\models\favorite
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="module_type", type="integer", description="Тип модуля"),
 *     @OA\Property(property="module_id", type="integer", description="ID модуля"),
 *     @OA\Property(property="user_id", type="integer", description="ID пользователя"),
 *     @OA\Property(property="created_at", type="string", description="Дата создания"),
 *     @OA\Property(property="updated_at", type="integer", description="Дата изменения")
 * )
 */
class Favorite extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'group_id',
            'module_type',
            'module_id',
            'user_id',
            'created_at',
            'updated_at',
            'user',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getGroup() {
        return $this->hasOne(FavoriteGroup::class, ['id' => 'group_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}