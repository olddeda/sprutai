<?php
namespace common\modules\payment\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class Kind
 * @package common\modules\payment\helpers\enum
 */
class Kind extends BaseEnum
{
	const ACCRUAL		= 0;
	const WITHDRAWAL	= 1;
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'payment-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::ACCRUAL 		=> 'kind_accrual',
		self::WITHDRAWAL   	=> 'kind_withdrawal',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}