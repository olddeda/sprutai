<?php
namespace api\models\achievement;

use common\modules\achievement\helpers\enum\Type;
use common\modules\achievement\models\Achievement as BaseModel;

use api\models\achievement\query\AchievementQuery;

/**
 * Class Achievement
 * @package api\models\achievement
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="type_id", type="integer", description="ID типа"),
 *     @OA\Property(property="type", type="string", description="Тип", enum={"owner", "review", "article", "news", "blog", "plugin", "comment", "likes", "liked", "subscribed"}),
 *     @OA\Property(property="title", type="string", description="Название"),
 *     @OA\Property(property="level", type="integer", description="Уровень"),
 *     @OA\Property(property="sequence", type="integer", description="Порядковый номер"),
 *     @OA\Property(property="status", type="integer", description="Статус"),
 *     @OA\Property(property="created_at", type="integer", description="Дата создания"),
 *     @OA\Property(property="updated_at", type="integer", description="Дата изменения")
 * )
 */
class Achievement extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'type_id' => function($data) {
                return $data->type;
            },
            'type' => function ($data) {
                return $data->getTypeName();
            },
            'title',
            'level',
            'sequence',
            'status',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @inheritdoc
     * @return AchievementQuery the active query used by this AR class.
     */
    public static function find() {
        return new AchievementQuery(get_called_class());
    }
}