<?php
namespace common\modules\payment\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class Status
 * @package common\modules\payment\helpers\enum
 */
class Status extends BaseEnum
{
	const WAIT			= 0;
	const PAID		    = 1;
	const DELIVERY     	= 10;
	const COMPLETED     = 20;
	const FAILED		= 30;
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'payment-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::WAIT 		=> 'status_wait',
		self::PAID   	=> 'status_paid',
		self::DELIVERY	=> 'status_delivery',
		self::COMPLETED	=> 'status_completed',
		self::FAILED	=> 'status_failed',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}