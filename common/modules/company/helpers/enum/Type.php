<?php
namespace common\modules\company\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class Type extends BaseEnum
{
	const VENDOR 			= 1 << 0;
	const INTEGRATOR 		= 1 << 1;
	const SHOP				= 1 << 2;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'company-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::VENDOR => 'type_vendor',
		self::INTEGRATOR => 'type_integrator',
		self::SHOP => 'type_shop',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}