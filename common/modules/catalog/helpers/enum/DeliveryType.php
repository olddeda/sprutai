<?php
namespace common\modules\catalog\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class DeliveryType extends BaseEnum
{
	const CDEK              = 1;
	const RUSSIAN_POST 		= 2;
	const EMS               = 3;
    const INTEGRAL          = 4;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'catalog-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::CDEK => 'delivery_type_cdek',
        self::RUSSIAN_POST => 'delivery_type_russian_post',
        self::EMS => 'delivery_type_ems',
        self::INTEGRAL => 'delivery_type_integral',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}