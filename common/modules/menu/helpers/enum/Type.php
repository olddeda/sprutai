<?php
namespace common\modules\menu\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class Type extends BaseEnum
{
	const TITLE 			= 0;
	const TAG		 		= 1;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'menu-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::TITLE => 'type_title',
		self::TAG => 'type_tag',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}