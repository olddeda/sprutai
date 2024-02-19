<?php
namespace common\modules\payment\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class StatusWithdrawal
 * @package common\modules\payment\helpers\enum
 */
class StatusWithdrawal extends BaseEnum
{
	const WAIT			= 0;
	const PAID		    = 1;
	const CANCELED		= 5;
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'payment-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::WAIT 		=> 'status_withdrawal_wait',
		self::PAID   	=> 'status_withdrawal_paid',
		self::CANCELED  => 'status_withdrawal_canceled',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}