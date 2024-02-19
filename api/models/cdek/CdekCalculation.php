<?php
namespace api\models\cdek;

use yii\base\Model;

/**
 * @OA\Schema(
 *     @OA\Property(property="days_min", type="integer", description="Минимальный срок", example=1),
 *     @OA\Property(property="days_max", type="string", description="Макимальный срок", example=3),
 *     @OA\Property(property="price", type="integer", description="Стоимость", example=300),
 *     @OA\Property(property="currency", type="string", description="Валюта", example="RUB")
 * )
 */
class CdekCalculation extends Model {

    /**
     * @var integer
     */
    public $days_min;

    /**
     * @var integer
     */
    public $days_max;

    /**
     * @var integer
     */
    public $price;

    /**
     * @var string
     */
    public $currency;
}