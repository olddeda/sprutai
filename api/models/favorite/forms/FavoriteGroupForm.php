<?php
namespace api\models\catalog\forms;

use api\models\favorite\FavoriteGroup;

/**
 *
 * @OA\RequestBody(
 *     request="FavoriteGroup",
 *     required=true,
 *     description="Данные группы избранного",
 *     @OA\JsonContent(
 *         @OA\Property(property="module_type", type="integer", description="Тип модуля"),
 *         @OA\Property(property="module_id", type="integer", description="ID модуля"),
 *         @OA\Property(property="title", type="string", description="Название"),
 *         example={
 *             "module_type": 40,
 *             "title": "Избранное"
 *         }
 *     )
 * )
 */

/**
 * Class FavoriteGroupForm
 * @package api\models\favorite\forms
 */
class FavoriteGroupForm extends FavoriteGroup
{
}