<?php
namespace api\models\telegram;

use api\traits\ImageTrait;
use common\modules\base\components\ArrayHelper;
use common\modules\telegram\models\TelegramChat as BaseModel;

/**
 * Class TelegramChat
 * @package api\models\telegram
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="identifier", type="integer", description="Идентификатор"),
 *     @OA\Property(property="title", type="string", description="Название"),
 *     @OA\Property(property="username", type="string", description="Никнейм"),
 *     @OA\Property(property="description", type="string", description="Описание"),
 *     @OA\Property(property="members_count", type="integer", description="Количество участников"),
 *     @OA\Property(property="notify_content", type="boolean", description="Уведомление при новом контенте"),
 *     @OA\Property(property="notify_payment", type="boolean", description="Уведомление при новом платеже"),
 *     @OA\Property(property="is_partner", type="boolean", description="Является партнером"),
 *     @OA\Property(property="is_channel", type="boolean", description="Является каналом"),
 *     @OA\Property(property="status", type="integer", description="Статус"),
 *     @OA\Property(property="created_by", type="integer", description="Создан пользователем"),
 *     @OA\Property(property="updated_by", type="integer", description="Изменен пользователем"),
 *     @OA\Property(property="created_at", type="integer", description="Дата создания"),
 *     @OA\Property(property="updated_at", type="integer", description="Дата изменения"),
 *     @OA\Property(property="image", ref="#/components/schemas/Image", description="Изображение"),
 *     @OA\Property(property="tags_ids", type="array", @OA\Items(type="integer"), description="IDs тегов"),
 *     @OA\Property(property="tags", type="array", @OA\Items(ref="#/components/schemas/Tag"), description="Теги (отдается при запросе ?expand=tags)")
 * )
 */
class TelegramChat extends BaseModel
{
    use ImageTrait;

    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'identifier',
            'title',
            'username',
            'description',
            'members_count',
            'notify_content',
            'notify_payment',
            'is_partner',
            'is_channel',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'image' => function($data) {
                return $data->mediaImageFor('logo');
            },
            'tags_ids' => function($data) {
                return ArrayHelper::getColumn($data->tags, 'id');
            }
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields() {
        return [
            'tags'
        ];
    }
}