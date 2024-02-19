<?php

namespace api\models\catalog;

use common\modules\base\helpers\enum\Status;
use yii\db\ActiveQuery;

use common\modules\catalog\models\CatalogFieldGroup as BaseModel;

/**
 * Class CatalogFieldGroup
 * @package api\models\catalog
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="title", type="string", description="Название"),
 *     @OA\Property(property="status", type="integer", description="Статус"),
 *     @OA\Property(property="created_by", type="integer", description="ID пользователя который создал"),
 *     @OA\Property(property="updated_by", type="integer", description="ID пользователя который последний изменял"),
 *     @OA\Property(property="created_at", type="integer", description="Дата создания"),
 *     @OA\Property(property="updated_at", type="integer", description="Дата обновления"),
 *     @OA\Property(property="fields", type="array", @OA\Items(ref="#/components/schemas/CatalogField"), description="Поля"),
 * )
 */
class CatalogFieldGroup extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'title',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'fields',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getFields(): ActiveQuery
    {
        return $this->hasMany(CatalogField::class, ['catalog_field_group_id' => 'id'])
            ->onCondition(['<>', CatalogField::tableName().'.status', Status::DELETED])
            ->where([])
            ;
    }
}