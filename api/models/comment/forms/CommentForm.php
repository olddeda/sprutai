<?php
namespace api\models\comment\forms;

use api\models\comment\Comment;

/**
 * @OA\RequestBody(
 *     request="Comment",
 *     required=true,
 *     description="Данные комментария",
 *     @OA\JsonContent(
 *         @OA\Property(property="module_type", type="integer", description="Тип модуля", enum={40}),
 *         @OA\Property(property="module_id", type="integer", description="ID модуля"),
 *         @OA\Property(property="parent_id", type="integer", description="ID родительской записи"),
 *         @OA\Property(property="content", type="string", description="Комментарий"),
 *         example={
 *             "module_type": 81,
 *             "module_id": 90,
 *             "parent_id": 0,
 *             "content": "Текст комментария"
 *         }
 *     )
 * )
 */

/**
 * Class CommentForm
 * @package api\models\comment\forms
 */
class CommentForm extends Comment
{

}