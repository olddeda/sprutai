<?php
namespace common\modules\payment\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class StatusUser
 * @package common\modules\payment\helpers\enum
 */
class StatusUser extends BaseEnum
{
	const WAIT			= 0;
	const PAID		    = 1;
	const COMPLETED     = 2;
	const FAILED		= 10;
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'payment-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::WAIT 		=> 'status_user_wait',
		self::PAID   	=> 'status_user_paid',
		self::COMPLETED	=> 'status_user_completed',
		self::FAILED	=> 'status_user_failed',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}