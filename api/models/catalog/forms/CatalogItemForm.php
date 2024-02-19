<?php
namespace api\models\catalog\forms;

use api\models\content\CatalogItem;

/**
 *
 * @OA\RequestBody(
 *     request="CatalogItem",
 *     required=true,
 *     description="Данные товара",
 *     @OA\JsonContent(
 *         @OA\Property(property="vendor_id", type="integer",description="ID производителя"),
 *         @OA\Property(property="yandex_id", type="integer",description="ID карточки товара в Yandex.Market"),
 *         @OA\Property(property="title",type="string", description="Название"),
 *         @OA\Property(property="model", type="string", description="Модель"),
 *         @OA\Property(property="url", type="string", description="Ссылка на товар на сайте производителя"),
 *         @OA\Property(property="comment", type="string", description="Комментарий"),
 *         @OA\Property(property="system_manufacturer", type="string", description="Системный: Производитель"),
 *         @OA\Property(property="system_model", type="string", description="Системный: Модель"),
 *         @OA\Property(property="status", type="integer", description="Статус"),
 *         @OA\Property(property="tags_ids", type="array", @OA\Items(type="integer")),
 *         example={
 *             "vendor_id": 149,
 *             "yandex_id": 1234567890,
 *             "title": "Netatmo Welcome",
 *             "model": "NSC01-EU",
 *             "url": "https://www.netatmo.com/en-us/security/cam-indoor",
 *             "comment": "Наполняется",
 *             "system_manufacturer": "",
 *             "system_model": "",
 *             "status": 5,
 *             "tags_ids": {7, 150}
 *         }
 *     )
 * )
 */

/**
 * Class CatalogItemForm
 * @package api\models\catalog\forms
 */
class CatalogItemForm extends CatalogItem
{

}