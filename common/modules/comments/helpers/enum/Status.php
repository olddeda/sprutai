<?php

namespace common\modules\comments\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class Status extends BaseEnum
{
	const TEMP			= 0;
	const ENABLED		= 1;
	const DELETED		= 2;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'comments-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::ENABLED 		=> 'enabled',
		self::DELETED 		=> 'deleted',
	];

	public static $exclude = [
		self::TEMP,
	];
}