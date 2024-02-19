<?php
namespace common\modules\content\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class PageType
 * @package common\modules\content\helpers\enum
 */
class PageType extends BaseEnum
{
	const TEXT	= 0;
	const PATH	= 1;
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'content-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::TEXT 	=> 'page_type_text',
		self::PATH 	=> 'page_type_path',
	];
	
	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}