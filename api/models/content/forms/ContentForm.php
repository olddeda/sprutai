<?php
namespace api\models\catalog\forms;

use api\models\content\Content;

/**
 *
 * @OA\RequestBody(
 *     request="ContentForm",
 *     required=true,
 *     description="Данные материала",
 *     @OA\JsonContent(
 *         @OA\Property(property="id", type="integer", description="ID"),
 *         @OA\Property(property="type", type="string", description="Тип"),
 *         @OA\Property(property="title", type="string", description="Название"),
 *         @OA\Property(property="description", type="string", description="Краткое описание"),
 *         @OA\Property(property="text", type="string", description="Текст"),
 *         @OA\Property(property="video_url", type="string", description="Ссылка на видео"),
 *         @OA\Property(property="status", type="integer", description="Статус"),
 *         @OA\Property(property="tags_ids", type="array", @OA\Items(type="integer"), description="IDs тегов"),
 *         @OA\Property(property="catalog_items_ids", type="array", @OA\Items(type="integer"), description="IDs устройств")
 *     )
 * )
 */

/**
 * Class ContentForm
 * @package api\models\content\forms
 */
class ContentForm extends Content {}