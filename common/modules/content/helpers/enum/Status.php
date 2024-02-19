<?php
namespace common\modules\content\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class Status
 * @package common\modules\base\helpers\enum
 */
class Status extends BaseEnum
{
	const TEMP			= 0;
	const ENABLED		= 1;
	const DISABLED		= 2;
	const DELETED		= 3;
	const MODERATED		= 4;
	const DRAFT			= 5;
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'content-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::TEMP 		=> 'temp',
		self::ENABLED 	=> 'enabled',
		self::DISABLED	=> 'disabled',
		self::DELETED	=> 'deleted',
		self::MODERATED	=> 'moderated',
		self::DRAFT		=> 'draft',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [
		self::TEMP,
		self::DELETED,
	];
}