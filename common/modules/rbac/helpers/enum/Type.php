<?php

namespace common\modules\rbac\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class Type
 * @package common\modules\rbac\helpers\enum
 */
class Type extends BaseEnum
{
	const ROLE			= 1;
	const PERMISSION	= 2;
	const TASK			= 3;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'rbac-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::ROLE 			=> 'role',
		self::TASK 			=> 'task',
		self::PERMISSION	=> 'permission',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}