<?php

namespace common\modules\settings\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class Type
 * @package common\modules\settings\helpers\enum
 */
class Type extends BaseEnum
{
    const STRING_TYPE = 'string';
    const INTEGER_TYPE = 'integer';
    const BOOLEAN_TYPE = 'boolean';
    const FLOAT_TYPE = 'float';
    const NULL_TYPE = 'null';

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'settings-enum';

    /**
     * @var array list of properties
     */
    public static $list = [
        self::STRING_TYPE => 'string',
        self::INTEGER_TYPE => 'integer',
        self::BOOLEAN_TYPE => 'boolean',
        self::FLOAT_TYPE => 'float',
        self::NULL_TYPE => 'null',
    ];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [
	];
}
