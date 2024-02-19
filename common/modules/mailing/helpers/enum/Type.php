<?php
namespace common\modules\mailing\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class Type extends BaseEnum
{
	const HUB 			= 0;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'mailing-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::HUB => 'hub',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}