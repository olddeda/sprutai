<?php
namespace api\models\catalog\forms;

use api\models\catalog\CatalogFieldGroup;

/**
 *
 * @OA\RequestBody(
 *     request="CatalogFieldGroup",
 *     required=true,
 *     description="Данные группы полей",
 *     @OA\JsonContent(
 *         @OA\Property(property="title",type="string", description="Название"),
 *         @OA\Property(property="status", type="integer", description="Статус"),
 *         example={
 *             "title": "Размеры",
 *             "status": 1,
 *         }
 *     )
 * )
 */

/**
 * Class CatalogFieldGroupForm
 * @package api\models\catalog\forms
 */
class CatalogFieldGroupForm extends CatalogFieldGroup
{

}