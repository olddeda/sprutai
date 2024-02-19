<?php
namespace api\models\content\swagger;

/**
 * @OA\Schema(
 *     @OA\Property(property="total", type="integer", description="Всего материалов"),
 *     @OA\Property(property="news", type="integer", description="Количество новостей"),
 *     @OA\Property(property="articles", type="integer", description="Количество статей"),
 *     @OA\Property(property="blogs", type="integer", description="Количество записей в блог"),
 *     @OA\Property(property="videos", type="integer", description="Количество видео")
 * )
 */
class ContentCount {}