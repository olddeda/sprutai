<?php

namespace api\models\catalog;

use yii\db\ActiveQuery;

use common\modules\catalog\models\CatalogField as BaseModel;

/**
 * Class CatalogField
 * @package api\models\catalog
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="catalog_field_group_id", type="integer", description="ID группы"),
 *     @OA\Property(property="type", type="integer", description="Тип", enum={0,1}),
 *     @OA\Property(property="format", type="integer", description="Формат", enum={0,1}),
 *     @OA\Property(property="title", type="string", description="Название"),
 *     @OA\Property(property="identifier", type="string", description="Идентификатор"),
 *     @OA\Property(property="unit", type="string", description="Единица измерения"),
 *     @OA\Property(property="sequence", type="integer", description="Порядковый номер"),
 *     @OA\Property(property="status", type="integer", description="Статус"),
 *     @OA\Property(property="created_by", type="integer", description="ID пользователя который создал"),
 *     @OA\Property(property="updated_by", type="integer", description="ID пользователя который последний изменял"),
 *     @OA\Property(property="created_at", type="integer", description="Дата создания"),
 *     @OA\Property(property="updated_at", type="integer", description="Дата обновления"),
 *     @OA\Property(property="group", ref="#/components/schemas/CatalogFieldGroup", description="Группа")
 * )
 */
class CatalogField extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'catalog_field_group_id',
            'type',
            'format',
            'title',
            'identifier',
            'unit',
            'sequence',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return [
            'group',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(CatalogFieldGroup::class, ['id' => 'catalog_field_group_id'])->where([]);
    }
}