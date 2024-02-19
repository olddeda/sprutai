<?php
namespace api\models\cdek\forms;

/**
 *
 * @OA\RequestBody(
 *     request="CdekCalculationForm",
 *     required=true,
 *     description="Данные расчета стоимости",
 *     @OA\JsonContent(
 *         @OA\Property(property="catalog_item_id", type="integer", description="ID товара", example=1809),
 *         @OA\Property(property="postal_code", type="string", description="Почтовый индекс", example=123181),
 *         @OA\Property(property="country", type="string", description="Страна", example="Россия"),
 *         @OA\Property(property="city", type="string", description="Город", example="Москва")
 *     )
 * )
 */

/**
 * Class CdekCalculationForm
 * @package api\models\cdek\forms
 *
 * @property integer $catalog_item_id
 * @property integer $postal_code
 * @property string $country
 * @property string $city
 */
class CdekCalculationForm {

    /**
     * @var integer
     */
    public $catalog_item_id;

    /**
     * @var integer
     */
    public $postal_code;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $city;

}