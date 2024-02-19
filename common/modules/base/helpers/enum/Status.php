<?php

namespace common\modules\base\helpers\enum;

/**
 * Class Type
 * @package common\modules\base\helpers\enum
 */
class Status extends BaseEnum
{
	const TEMP			= 0;
	const ENABLED		= 1;
	const DISABLED		= 2;
	const DELETED		= 3;
	const PROCESS		= 10;

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::TEMP 		=> 'temp',
		self::ENABLED 	=> 'enabled',
		self::DISABLED	=> 'disabled',
		self::DELETED	=> 'deleted',
		self::PROCESS 	=> 'process',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [
		self::TEMP,
		self::DELETED,
		self::PROCESS,
	];
}