<?php
namespace common\modules\catalog\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class FieldType extends BaseEnum
{
	const TEXT  = 0;
	const RANGE = 1;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'catalog-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::TEXT => 'field_type_text',
        self::RANGE => 'field_type_range',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}