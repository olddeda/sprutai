<?php

namespace common\modules\tag\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class Type extends BaseEnum
{
	const NONE			= 1 << 0;
	const SYSTEM		= 1 << 1;

	const VENDOR        = 1 << 2;
    const TYPE          = 1 << 3;
    const PLATFORM      = 1 << 4;
    const PROTOCOL      = 1 << 5;
    const FILTER        = 1 << 6;
    const FILTER_GROUP  = 1 << 7;
    const COMPANY       = 1 << 8;

	const SPECIAL       = 1 << 16;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'tag-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::NONE 		       => 'none',
		self::SYSTEM	       => 'system',
        self::VENDOR           => 'vendor',
        self::TYPE             => 'type',
        self::PLATFORM         => 'platform',
        self::PROTOCOL         => 'protocol',
        self::FILTER_GROUP     => 'filter_group',
        self::FILTER           => 'filter',
        self::COMPANY          => 'company',
		self::SPECIAL	       => 'special',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}