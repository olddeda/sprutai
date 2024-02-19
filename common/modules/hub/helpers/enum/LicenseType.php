<?php
namespace common\modules\hub\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class LicenseType
 * @package common\modules\hub\heplers\enum
 */
class LicenseType extends BaseEnum
{
	const ZIGBEE        = 1;
    const ZWAVE         = 2;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'hub-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::ZIGBEE 		=> 'license_type_zigbee',
        self::ZWAVE 		=> 'license_type_zwave',
	];
	
	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}