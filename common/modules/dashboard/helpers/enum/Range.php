<?php
namespace common\modules\dashboard\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class Range
 * @package common\modules\telegram\helpers\enum
 */
class Range extends BaseEnum
{
	const DAY			= 0;
	const WEEK			= 1;
	const MONTH			= 3;
	const YEAR			= 4;
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'dashboard-enum';
	
	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::DAY 	=> 'range_day',
		self::WEEK	=> 'range_week',
		self::MONTH => 'range_month',
		self::YEAR  => 'range_year',
	];
	
	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}