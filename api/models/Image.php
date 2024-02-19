<?php
namespace api\models;

/**
 * @OA\Schema(
 *     @OA\Property(property="http", type="string", description="Полный путь к изображению"),
 *     @OA\Property(property="path", type="string", description="Путь к изображению"),
 *     @OA\Property(property="file", type="string", description="Файл изображения"),
 *     @OA\Property(property="original", type="string", description="Оригинал изображения")
 * )
 */
class Image {}