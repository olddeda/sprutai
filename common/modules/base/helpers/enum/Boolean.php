<?php

namespace common\modules\base\helpers\enum;

class Boolean extends BaseEnum
{
	const YES = 1;
	const NO = 0;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'base-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::YES => 'yes',
		self::NO => 'no',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [
	];
}