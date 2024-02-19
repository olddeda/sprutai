<?php

namespace api\models\catalog;

use yii\db\ActiveQuery;

use common\modules\catalog\models\CatalogItemField as BaseModel;

/**
 * Class CatalogItemField
 * @package api\models\catalog
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="catalog_item_id", type="integer", description="ID товара"),
 *     @OA\Property(property="catalog_field_group_id", type="integer", description="ID группы полей"),
 *     @OA\Property(property="catalog_field_id", type="integer", description="ID поля"),
 *     @OA\Property(property="catalog_tag_id", type="integer", description="ID тега"),
 *     @OA\Property(property="type", type="integer", description="Тип", enum={0,1}),
 *     @OA\Property(property="format", type="integer", description="Формат", enum={0,1}),
 *     @OA\Property(property="name", type="string", description="Название"),
 *     @OA\Property(property="value", type="string", description="Значение"),
 *     @OA\Property(property="title", type="string", description="Название"),
 *     @OA\Property(property="identifier", type="string", description="Идентификатор"),
 *     @OA\Property(property="unit", type="string", description="Единица измерения"),
 *     @OA\Property(property="sequence", type="integer", description="Порядковый номер"),
 *     @OA\Property(property="status", type="integer", description="Статус"),
 * )
 */
class CatalogItemField extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'catalog_item_id',
            'catalog_field_group_id',
            'catalog_field_id',
            'tag_id',
            'type',
            'format',
            'name',
            'value',
            'title',
            'identifier',
            'unit',
            'sequence',
            'status',
        ];
    }
}