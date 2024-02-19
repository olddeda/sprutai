<?php
namespace common\modules\user\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class WalletType
 * @package common\modules\user\heplers\enum
 */
class WalletType extends BaseEnum
{
    const NONE          = 0;
	const YANDEX        = 1;
    const PAYPAL        = 2;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'user-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
        self::NONE		    => 'wallet_type_none',
		self::YANDEX		=> 'wallet_type_yandex',
        self::PAYPAL        => 'wallet_type_paypal',
	];
	
	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}