<?php
namespace common\modules\content\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class StatusUnique
 * @package common\modules\base\helpers\enum
 */
class StatusUnique extends BaseEnum
{
	const QUEUE			= 0;
	const COMPLETE		= 1;
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'content-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::QUEUE 	=> 'status_unique_queue',
		self::COMPLETE	=> 'status_unique_complete',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}