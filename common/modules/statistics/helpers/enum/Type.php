<?php
namespace common\modules\statistics\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class Type
 * @package common\modules\statistics\helpers\enum
 */
class Type extends BaseEnum
{
	const SHOW			= 0;
	const VISIT			= 1;
	const OUTGOING		= 2;
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'statistics-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::SHOW 		=> 'show',
		self::VISIT 	=> 'visit',
		self::OUTGOING	=> 'outgoing',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [
	];
}