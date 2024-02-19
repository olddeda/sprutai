<?php

namespace common\modules\rbac\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class Role
 * @package common\modules\rbac\helpers\enum
 */
class Role extends BaseEnum
{
	const SUPERADMIN	= 1;
	const ADMIN			= 2;
	const USER			= 3;
	const EDITOR		= 4;
	const EDITOR_NEWS	= 5;
	const COMPANY		= 6;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'rbac-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::SUPERADMIN 	=> 'SuperAdmin',
		self::ADMIN 		=> 'Admin',
		self::USER			=> 'User',
		self::EDITOR		=> 'Editor',
		self::EDITOR_NEWS	=> 'EditorNews',
		self::COMPANY		=> 'Company',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}