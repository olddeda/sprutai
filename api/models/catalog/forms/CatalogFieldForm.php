<?php
namespace api\models\catalog\forms;

use api\models\catalog\CatalogField;

/**
 *
 * @OA\RequestBody(
 *     request="CatalogField",
 *     required=true,
 *     description="Данные поля",
 *     @OA\JsonContent(
 *         @OA\Property(property="catalog_field_group_id",type="integer", description="ID группы полей"),
 *         @OA\Property(property="type",type="integer", description="Тип"),
 *         @OA\Property(property="format", type="integer", description="Формат"),
 *         @OA\Property(property="title",type="string", description="Название"),
 *         @OA\Property(property="identifier",type="string", description="Идентификатор"),
 *         @OA\Property(property="status", type="integer", description="Статус"),
 *         example={
 *             "catalog_field_group_id": 1,
 *             "type": 0,
 *             "format": 0,
 *             "title": "Ширина",
 *             "identifier": "width",
 *             "status": 1,
 *         }
 *     )
 * )
 */

/**
 * Class CatalogFieldForm
 * @package api\models\catalog\forms
 */
class CatalogFieldForm extends CatalogField
{

}