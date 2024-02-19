<?php
namespace common\modules\catalog\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class CorrectType extends BaseEnum
{
	const TITLE             = 0;
	const MODEL             = 1;
	const TAG   		    = 20;
	const FIELD             = 40;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'catalog-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::TITLE => 'correct_type_title',
        self::MODEL => 'correct_type_model',
		self::TAG => 'correct_type_tag',
        self::FIELD => 'correct_type_field',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}