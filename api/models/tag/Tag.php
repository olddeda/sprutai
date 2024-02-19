<?php
namespace api\models\tag;

use Yii;
use yii\db\ActiveQuery;

use common\modules\tag\models\Tag as BaseModel;

use api\models\catalog\CatalogFieldGroup;

use api\traits\ImageTrait;

/**
 * Class Tag
 * @package api\models\tag
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="type", type="string", enum={"none", "system", "vendor", "type", "platform", "protocol", "filter_group", "filter", "special"}, description="Тип"),
 *     @OA\Property(property="title", type="string", description="Название"),
 *     @OA\Property(property="multiple", type="boolean", description="Мультивыбор"),
 *     @OA\Property(property="visible_preview", type="boolean", description="Отображение в превью"),
 *     @OA\Property(property="sequence", type="integer", description="Порядковый номер"),
 *     @OA\Property(property="status", type="integer", description="Статус"),
 *     @OA\Property(property="created_by", type="integer", description="Создан пользователем"),
 *     @OA\Property(property="updated_by", type="integer", description="Изменен пользователем"),
 *     @OA\Property(property="created_at", type="integer", description="Дата создания"),
 *     @OA\Property(property="updated_at", type="integer", description="Дата изменения"),
 *     @OA\Property(property="links_ids", type="array", @OA\Items(type="integer"), description="ID связей с тегами"),
 *     @OA\Property(property="links", type="array", @OA\Items(ref="#/components/schemas/Tag"), description="Связь с тегами"),
 *     @OA\Property(property="catalog_field_group_ids", type="array", @OA\Items(type="integer"), description="ID связей с группами полей"),
 *     @OA\Property(property="catalog_field_groups", type="array", @OA\Items(ref="#/components/schemas/CatalogFieldGroup"), description="Связь с группами полей"),
 *     @OA\Property(property="image", ref="#/components/schemas/Image", description="Изображение")
 * )
 */
class Tag extends BaseModel
{
    use ImageTrait;

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'type',
            'title',
            'multiple',
            'visible_preview',
            'sequence',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'links_ids' => function($data) {
                if ($data->is_vendor) {
                    return [];
                }
                return !$data->is_filter ? $data->links_ids : [];
            },
            'links' => function($data) {
                if ($data->is_vendor) {
                    return [];
                }
                return !$data->is_filter ? $data->links : [];
            },
            'catalog_field_group_ids' => function ($data) {
                return $data->is_type ? $data->catalog_field_group_ids : [];
            },
            'catalog_field_groups' => function ($data) {
                return $data->is_type ? $data->catalogFieldGroups : [];
            }
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return [
            'image' => function($data) {
                return $data->mediaImageFor('image');
            },
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getLinks(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('tagModule')->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCatalogFieldGroups(): ActiveQuery
    {
        return $this->hasMany(CatalogFieldGroup::class, ['id' => 'catalog_field_group_id'])->via('catalogFieldGroupTags');
    }

    /**
     * @return array|null
     */
    public function getMediaImage(): ?array
    {
        $image = $this->image;
        if ($image) {
            $imageInfo = $image->getImageInfo(true);
            return [
                'path' => $imageInfo['http'],
                'file' => $imageInfo['file'],
                'original' => $imageInfo['original'],
            ];
        }
        return null;
    }

}
