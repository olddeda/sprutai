<?php
namespace common\modules\user\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class Status extends BaseEnum
{
	const TEMP			= 0;
	const PUBLISHED		= 1;
	const DRAFT			= 2;
	const DELETED		= 3;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'user-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::TEMP 		=> 'temp',
		self::PUBLISHED => 'published',
		self::DRAFT		=> 'draft',
		self::DELETED	=> 'deleted',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [
		self::TEMP,
		self::DELETED,
	];
}