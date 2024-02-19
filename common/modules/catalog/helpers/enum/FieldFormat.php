<?php
namespace common\modules\catalog\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class FieldFormat extends BaseEnum
{
	const STRING    = 0;
	const NUMBER    = 1;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'catalog-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::STRING => 'field_format_string',
		self::NUMBER => 'field_format_number',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}